<?php

namespace App\Services\Pterodactyl;

use App\Models\PterodactylNode;
use App\Models\PterodactylEgg;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class PterodactylService
{
    protected ?PterodactylNode $node = null;
    protected ?string $apiKey = null;
    protected ?string $panelUrl = null;

    public function __construct(?PterodactylNode $node = null)
    {
        if ($node) {
            $this->node = $node;
            $this->apiKey = $node->api_key;
            $this->panelUrl = rtrim($node->panel_url, '/');
        } else {
            // Use default/first active node
            $this->node = PterodactylNode::where('is_active', true)->first();
            if ($this->node) {
                $this->apiKey = $this->node->api_key;
                $this->panelUrl = rtrim($this->node->panel_url, '/');
            }
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->node) && !empty($this->apiKey) && !empty($this->panelUrl);
    }

    protected function ensureConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('No active Pterodactyl node is configured.');
        }
    }

    /**
     * Create a new user in Pterodactyl panel
     */
    public function createUser(User $user): ?array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post("{$this->panelUrl}/api/application/users", [
            'email' => $user->email,
            'username' => $this->generateUsername($user),
            'first_name' => $user->first_name ?? $user->name,
            'last_name' => $user->last_name ?? 'User',
        ]);

        if ($response->successful()) {
            return $response->json()['attributes'] ?? null;
        }

        return null;
    }

    /**
     * Get or create Pterodactyl user for local user
     */
    public function getOrCreateUser(User $user): ?array
    {
        $this->ensureConfigured();

        // Try to find existing user
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->get("{$this->panelUrl}/api/application/users?filter[email]={$user->email}");

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['data'])) {
                return $data['data'][0]['attributes'];
            }
        }

        // Create new user if not found
        return $this->createUser($user);
    }

    /**
     * Create a new server
     */
    public function createServer(Service $service, array $config): ?array
    {
        $this->ensureConfigured();

        $user = $this->getOrCreateUser($service->user);
        
        if (!$user) {
            throw new \Exception('Failed to get or create Pterodactyl user');
        }

        $egg = PterodactylEgg::find($config['egg_id'] ?? null);
        
        if (!$egg) {
            throw new \Exception('Invalid egg selected');
        }

        $serverData = [
            'name' => $config['name'] ?? $service->name,
            'user' => $user['id'],
            'egg' => (int) $egg->egg_id,
            'nest' => (int) $egg->nest_id,
            'docker_image' => $egg->docker_image,
            'startup' => $egg->startup_command['default'] ?? '',
            'environment' => $config['environment'] ?? [],
            'limits' => [
                'memory' => ($config['memory'] ?? 1024),
                'swap' => ($config['swap'] ?? 0),
                'disk' => ($config['disk'] ?? 10240),
                'io' => 500,
                'cpu' => ($config['cpu'] ?? 100),
            ],
            'feature_limits' => [
                'databases' => $config['databases'] ?? 0,
                'backups' => $config['backups'] ?? 1,
                'allocations' => $config['allocations'] ?? 1,
            ],
            'allocation' => [
                'default' => (int) ($config['allocation_id'] ?? 0),
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post("{$this->panelUrl}/api/application/servers", $serverData);

        if ($response->successful()) {
            $serverData = $response->json()['attributes'] ?? null;
            
            if ($serverData) {
                $service->update([
                    'panel_server_id' => $serverData['id'],
                    'panel_credentials' => [
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'server_id' => $serverData['id'],
                    ],
                ]);
                
                $this->node->incrementServerCount();
            }
            
            return $serverData;
        }

        $error = $response->json()['errors'][0]['detail'] ?? 'Unknown error';
        throw new \Exception("Failed to create server: {$error}");
    }

    /**
     * Suspend a server
     */
    public function suspendServer(Service $service): bool
    {
        $this->ensureConfigured();

        if (!$service->panel_server_id) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->post("{$this->panelUrl}/api/application/servers/{$service->panel_server_id}/suspend");

        return $response->successful();
    }

    /**
     * Unsuspend a server
     */
    public function unsuspendServer(Service $service): bool
    {
        $this->ensureConfigured();

        if (!$service->panel_server_id) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->post("{$this->panelUrl}/api/application/servers/{$service->panel_server_id}/unsuspend");

        return $response->successful();
    }

    /**
     * Delete a server
     */
    public function deleteServer(Service $service): bool
    {
        $this->ensureConfigured();

        if (!$service->panel_server_id) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->delete("{$this->panelUrl}/api/application/servers/{$service->panel_server_id}");

        if ($response->successful()) {
            $this->node->decrementServerCount();
            return true;
        }

        return false;
    }

    /**
     * Get server status
     */
    public function getServerStatus(Service $service): ?array
    {
        $this->ensureConfigured();

        if (!$service->panel_server_id) {
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->get("{$this->panelUrl}/api/application/servers/{$service->panel_server_id}");

        if ($response->successful()) {
            return $response->json()['attributes'] ?? null;
        }

        return null;
    }

    /**
     * Get server resource usage
     */
    public function getServerResources(Service $service): ?array
    {
        $this->ensureConfigured();

        if (!$service->panel_server_id) {
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->get("{$this->panelUrl}/api/application/servers/{$service->panel_server_id}/resources");

        if ($response->successful()) {
            return $response->json()['attributes'] ?? null;
        }

        return null;
    }

    /**
     * Get available allocations for a node
     */
    public function getAllocations(int $nodeId): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->get("{$this->panelUrl}/api/application/nodes/{$nodeId}/allocations");

        if ($response->successful()) {
            $allocations = $response->json()['data'] ?? [];
            // Filter only unassigned allocations
            return array_filter($allocations, fn($a) => !$a['attributes']['assigned']);
        }

        return [];
    }

    /**
     * Reinstall a server
     */
    public function reinstallServer(Service $service): bool
    {
        $this->ensureConfigured();

        if (!$service->panel_server_id) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->post("{$this->panelUrl}/api/application/servers/{$service->panel_server_id}/reinstall");

        return $response->successful();
    }

    /**
     * Generate a unique username
     */
    protected function generateUsername(User $user): string
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->name));
        $username = $base . $user->id;
        
        if (strlen($username) > 16) {
            $username = substr($username, 0, 16);
        }
        
        return $username;
    }

    /**
     * Generate panel URL for client
     */
    public function getPanelUrl(Service $service): string
    {
        $this->ensureConfigured();

        if ($service->panel_server_id) {
            return "{$this->panelUrl}/server/{$service->panel_server_id}";
        }
        
        return $this->panelUrl;
    }
}
