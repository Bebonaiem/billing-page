<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Pterodactyl\PterodactylService;
use Livewire\Component;
use Livewire\WithPagination;

class Services extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public ?int $userId = null;
    public bool $showModal = false;
    public ?Service $editingService = null;
    public string $modalAction = '';
    public array $serverStatus = [];
    public array $provisionPreview = [];

    protected $queryString = ['search', 'status', 'userId'];

    public function mount()
    {
        $this->editingService = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewService(int $serviceId)
    {
        $this->editingService = Service::with(['user', 'product', 'order', 'orderItem'])->findOrFail($serviceId);
        
        // Get server status from Pterodactyl if applicable
        if ($this->editingService->panel_type === 'pterodactyl' && $this->editingService->panel_server_id) {
            try {
                $pterodactyl = new PterodactylService();
                $this->serverStatus = $pterodactyl->getServerStatus($this->editingService) ?? [];
            } catch (\Exception $e) {
                $this->serverStatus = [];
            }
        }

        if ($this->editingService->panel_type === 'pterodactyl' && !$this->editingService->panel_server_id) {
            $this->provisionPreview = $this->buildProvisionPreview($this->editingService);
        }
        
        $this->modalAction = 'view';
        $this->showModal = true;
    }

    public function confirmAction(int $serviceId, string $action)
    {
        $this->editingService = Service::with(['user', 'product', 'order', 'orderItem'])->findOrFail($serviceId);
        $this->modalAction = $action;
        $this->showModal = true;
    }

    public function executeAction()
    {
        if (!$this->editingService) {
            return;
        }

        $pterodactyl = new PterodactylService();

        match ($this->modalAction) {
            'suspend' => $this->handleSuspend($pterodactyl),
            'unsuspend' => $this->handleUnsuspend($pterodactyl),
            'terminate' => $this->handleTerminate($pterodactyl),
            'provision' => $this->handleProvision($pterodactyl),
            'reinstall' => $this->handleReinstall($pterodactyl),
            default => null,
        };

        $messages = [
            'suspend' => 'Service suspended successfully.',
            'unsuspend' => 'Service unsuspended successfully.',
            'terminate' => 'Service terminated successfully.',
            'provision' => 'Service provisioned successfully.',
            'reinstall' => 'Service reinstalled successfully.',
        ];

        session()->flash('success', $messages[$this->modalAction] ?? 'Service updated successfully.');
        $this->closeModal();
    }

    protected function handleSuspend(PterodactylService $pterodactyl): void
    {
        $this->editingService->suspend();
        if ($this->editingService->panel_server_id) {
            $pterodactyl->suspendServer($this->editingService);
        }
    }

    protected function handleUnsuspend(PterodactylService $pterodactyl): void
    {
        $this->editingService->unsuspend();
        if ($this->editingService->panel_server_id) {
            $pterodactyl->unsuspendServer($this->editingService);
        }
    }

    protected function handleTerminate(PterodactylService $pterodactyl): void
    {
        if ($this->editingService->panel_server_id) {
            $pterodactyl->deleteServer($this->editingService);
        }
        $this->editingService->terminate();
    }

    protected function handleProvision(PterodactylService $pterodactyl): void
    {
        if ($this->editingService->panel_server_id) {
            return;
        }

        $pterodactyl->createServer($this->editingService, $this->buildProvisionPreview($this->editingService));
        $this->editingService->update(['status' => 'active']);
    }

    protected function handleReinstall(PterodactylService $pterodactyl): void
    {
        if ($this->editingService->panel_server_id) {
            $pterodactyl->reinstallServer($this->editingService);
        }
    }

    protected function buildProvisionPreview(Service $service): array
    {
        $integrationSettings = $service->product?->integration_settings ?? [];
        return [
            'egg_id' => $integrationSettings['egg_id'] ?? null,
            'memory' => $integrationSettings['memory'] ?? 1024,
            'disk' => $integrationSettings['disk'] ?? 10240,
            'cpu' => $integrationSettings['cpu'] ?? 100,
            'swap' => $integrationSettings['swap'] ?? 0,
            'databases' => $integrationSettings['databases'] ?? 0,
            'backups' => $integrationSettings['backups'] ?? 1,
            'allocations' => $integrationSettings['allocations'] ?? 1,
            'name' => $service->name ?? $service->product?->name ?? 'Service',
            'environment' => $integrationSettings['environment'] ?? [],
        ];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingService = null;
        $this->modalAction = '';
        $this->serverStatus = [];
        $this->provisionPreview = [];
    }

    public function render()
    {
        $query = Service::query()
            ->with(['user', 'product', 'orderItem'])
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($q) {
                            $q->where('email', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->userId, function ($query) {
                $query->where('user_id', $this->userId);
            });

        $services = $query->latest()->paginate(15);
        $users = User::orderBy('name')->get();

        return view('livewire.admin.services', [
            'services' => $services,
            'users' => $users,
        ]);
    }
}
