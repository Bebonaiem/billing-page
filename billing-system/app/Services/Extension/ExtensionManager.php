<?php

namespace App\Services\Extension;

use App\Models\Extension;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ExtensionManager
{
    protected string $extensionsPath;

    public function __construct()
    {
        $this->extensionsPath = base_path('extensions');
    }

    /**
     * Get all available extensions
     */
    public function getAvailableExtensions(): array
    {
        $extensions = [];

        if (!File::exists($this->extensionsPath)) {
            return $extensions;
        }

        $directories = File::directories($this->extensionsPath);

        foreach ($directories as $directory) {
            $extensionFile = $directory . '/extension.json';
            
            if (File::exists($extensionFile)) {
                $config = json_decode(File::get($extensionFile), true);
                if ($config) {
                    $extensions[] = $config;
                }
            }
        }

        return $extensions;
    }

    /**
     * Install an extension
     */
    public function install(string $extensionKey): bool
    {
        try {
            // Check dependencies
            $available = $this->getAvailableExtensions();
            $config = collect($available)->firstWhere('key', $extensionKey);

            if (!$config) {
                throw new \Exception("Extension not found: {$extensionKey}");
            }

            // Check dependencies
            if (!empty($config['dependencies'])) {
                foreach ($config['dependencies'] as $dependency) {
                    if (!$this->isExtensionActive($dependency)) {
                        throw new \Exception("Missing dependency: {$dependency}");
                    }
                }
            }

            // Create extension record
            Extension::create([
                'name' => $config['name'],
                'slug' => $extensionKey,
                'version' => $config['version'] ?? '1.0.0',
                'author' => $config['author'] ?? 'Unknown',
                'description' => $config['description'] ?? '',
                'installed_at' => now(),
                'activated_at' => now(),
                'is_active' => true,
                'settings' => [],
            ]);

            // Run install hook if exists
            $this->runHook($extensionKey, 'install');

            return true;
        } catch (\Exception $e) {
            Log::error('Extension installation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Uninstall an extension
     */
    public function uninstall(string $extensionKey): bool
    {
        try {
            $extension = Extension::where('slug', $extensionKey)->first();
            
            if (!$extension) {
                return false;
            }

            // Run uninstall hook
            $this->runHook($extensionKey, 'uninstall');

            $extension->delete();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Extension uninstallation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Activate an extension
     */
    public function activate(string $extensionKey): bool
    {
        $extension = Extension::where('slug', $extensionKey)->first();
        
        if (!$extension) {
            return false;
        }

        $extension->update(['is_active' => true, 'activated_at' => now()]);
        $this->runHook($extensionKey, 'activate');
        
        return true;
    }

    /**
     * Deactivate an extension
     */
    public function deactivate(string $extensionKey): bool
    {
        $extension = Extension::where('slug', $extensionKey)->first();
        
        if (!$extension) {
            return false;
        }

        $extension->update(['is_active' => false]);
        $this->runHook($extensionKey, 'deactivate');
        
        return true;
    }

    /**
     * Check if extension is active
     */
    public function isExtensionActive(string $extensionKey): bool
    {
        return Extension::where('slug', $extensionKey)->where('is_active', true)->exists();
    }

    /**
     * Run extension hook
     */
    protected function runHook(string $extensionKey, string $hook): void
    {
        $hookFile = $this->extensionsPath . '/' . $extensionKey . '/hooks/' . $hook . '.php';
        
        if (File::exists($hookFile)) {
            try {
                include $hookFile;
            } catch (\Exception $e) {
                Log::error("Hook execution failed for {$extensionKey}:{$hook} - " . $e->getMessage());
            }
        }
    }

    /**
     * Get extension settings
     */
    public function getSettings(string $extensionKey): array
    {
        $extension = Extension::where('slug', $extensionKey)->first();
        return $extension?->settings ?? [];
    }

    /**
     * Save extension settings
     */
    public function saveSettings(string $extensionKey, array $settings): bool
    {
        $extension = Extension::where('slug', $extensionKey)->first();
        
        if (!$extension) {
            return false;
        }

        $extension->update(['settings' => $settings]);
        return true;
    }

    /**
     * Dispatch event to all active extensions
     */
    public function dispatchEvent(string $event, array $data = []): void
    {
        $extensions = Extension::where('is_active', true)->get();

        foreach ($extensions as $extension) {
            $eventFile = $this->extensionsPath . '/' . $extension->slug . '/events/' . $event . '.php';
            
            if (File::exists($eventFile)) {
                try {
                    $callback = include $eventFile;
                    if (is_callable($callback)) {
                        $callback($data);
                    }
                } catch (\Exception $e) {
                    Log::error("Event dispatch failed for {$extension->slug}:{$event} - " . $e->getMessage());
                }
            }
        }
    }
}
