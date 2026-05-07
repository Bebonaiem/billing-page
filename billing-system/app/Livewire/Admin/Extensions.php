<?php

namespace App\Livewire\Admin;

use App\Models\Extension;
use App\Services\Extension\ExtensionManager;
use Livewire\Component;
use Livewire\WithPagination;

class Extensions extends Component
{
    use WithPagination;

    protected ExtensionManager $extensionManager;
    
    public string $search = '';
    public bool $showInstallModal = false;
    public ?string $installingExtension = null;
    public ?Extension $editingExtension = null;
    public array $extensionSettings = [];

    protected $queryString = ['search'];

    public function boot()
    {
        $this->extensionManager = new ExtensionManager();
    }

    public function mount()
    {
        $this->editingExtension = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showAvailableExtensions()
    {
        $this->showInstallModal = true;
    }

    public function installExtension(string $extensionKey)
    {
        $this->installingExtension = $extensionKey;
        
        $success = $this->extensionManager->install($extensionKey);
        
        if ($success) {
            session()->flash('success', 'Extension installed successfully.');
        } else {
            session()->flash('error', 'Failed to install extension.');
        }
        
        $this->installingExtension = null;
        $this->showInstallModal = false;
    }

    public function toggleExtension(int $extensionId)
    {
        $extension = Extension::findOrFail($extensionId);
        
        if ($extension->is_active) {
            $this->extensionManager->deactivate($extension->slug);
            session()->flash('success', 'Extension deactivated.');
        } else {
            $this->extensionManager->activate($extension->slug);
            session()->flash('success', 'Extension activated.');
        }
    }

    public function uninstallExtension(int $extensionId)
    {
        $extension = Extension::findOrFail($extensionId);
        
        $success = $this->extensionManager->uninstall($extension->slug);
        
        if ($success) {
            session()->flash('success', 'Extension uninstalled successfully.');
        } else {
            session()->flash('error', 'Failed to uninstall extension.');
        }
    }

    public function editSettings(int $extensionId)
    {
        $this->editingExtension = Extension::findOrFail($extensionId);
        $this->extensionSettings = $this->editingExtension->settings ?? [];
    }

    public function saveSettings()
    {
        if ($this->editingExtension) {
            $this->extensionManager->saveSettings($this->editingExtension->slug, $this->extensionSettings);
            session()->flash('success', 'Settings saved successfully.');
            $this->editingExtension = null;
        }
    }

    public function closeModal()
    {
        $this->showInstallModal = false;
        $this->editingExtension = null;
        $this->installingExtension = null;
    }

    public function render()
    {
        $query = Extension::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            });

        $extensions = $query->latest()->paginate(15);
        
        // Get available extensions that are not installed
        $availableExtensions = [];
        if ($this->showInstallModal) {
            $installedSlugs = Extension::pluck('slug')->toArray();
            $allAvailable = $this->extensionManager->getAvailableExtensions();
            
            $availableExtensions = collect($allAvailable)
                ->filter(fn($ext) => !in_array($ext['key'], $installedSlugs))
                ->toArray();
        }

        return view('livewire.admin.extensions', [
            'extensions' => $extensions,
            'availableExtensions' => $availableExtensions,
        ]);
    }
}
