<?php

namespace App\Services\Extension;

use App\Models\Extension;
use App\Models\Service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ExtensionService
{
    protected string $extensionPath = 'app/Extensions';

    /**
     * Load all enabled extensions
     */
    public function loadExtensions(): array
    {
        $extensions = Extension::where('is_enabled', true)->get();
        $loaded = [];

        foreach ($extensions as $extension) {
            try {
                $loaded[$extension->name] = $this->loadExtension($extension);
            } catch (\Exception $e) {
                Log::error("Failed to load extension {$extension->name}: " . $e->getMessage());
            }
        }

        return $loaded;
    }

    /**
     * Load a single extension
     */
    public function loadExtension(Extension $extension): ?array
    {
        $configPath = base_path($this->extensionPath . '/' . $extension->name . '/config.php');

        if (!File::exists($configPath)) {
            throw new \RuntimeException("Extension config not found: {$configPath}");
        }

        return include $configPath;
    }

    /**
     * Get extension status
     */
    public function getStatus(Extension $extension): string
    {
        return $extension->is_enabled ? 'active' : 'inactive';
    }

    /**
     * Enable extension
     */
    public function enable(Extension $extension): bool
    {
        try {
            $extension->update(['is_enabled' => true]);
            Log::info("Extension enabled: {$extension->name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to enable extension: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Disable extension
     */
    public function disable(Extension $extension): bool
    {
        try {
            $extension->update(['is_enabled' => false]);
            Log::info("Extension disabled: {$extension->name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to disable extension: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Install extension
     */
    public function install(string $extensionName): bool
    {
        try {
            $extension = Extension::updateOrCreate(
                ['name' => $extensionName],
                ['is_enabled' => true, 'version' => '1.0.0']
            );

            Log::info("Extension installed: {$extensionName}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to install extension: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Uninstall extension
     */
    public function uninstall(Extension $extension): bool
    {
        try {
            $extension->delete();
            Log::info("Extension uninstalled: {$extension->name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to uninstall extension: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all available extensions
     */
    public function getAvailable(): array
    {
        $extensionsDir = base_path($this->extensionPath);
        $available = [];

        if (File::isDirectory($extensionsDir)) {
            $directories = File::directories($extensionsDir);

            foreach ($directories as $dir) {
                $name = basename($dir);
                $available[] = $name;
            }
        }

        return $available;
    }

    /**
     * Get services for extension
     */
    public function getServices(Extension $extension): array
    {
        return Service::where('extension_id', $extension->id)->get()->toArray();
    }
}
