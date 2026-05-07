<?php

namespace App\Livewire\Client;

use App\Models\Service;
use App\Services\Pterodactyl\PterodactylService;
use Livewire\Component;
use Livewire\WithPagination;

class Services extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public bool $showModal = false;
    public ?Service $viewingService = null;
    public array $serverStatus = [];

    protected $queryString = ['search', 'status'];

    public function mount()
    {
        $this->viewingService = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewService(int $serviceId)
    {
        $this->viewingService = Service::with(['product', 'order'])
            ->where('user_id', auth()->id())
            ->findOrFail($serviceId);
        
        // Get server status from Pterodactyl if applicable
        if ($this->viewingService->panel_type === 'pterodactyl' && $this->viewingService->panel_server_id) {
            try {
                $pterodactyl = new PterodactylService();
                $this->serverStatus = $pterodactyl->getServerStatus($this->viewingService) ?? [];
            } catch (\Exception $e) {
                $this->serverStatus = [];
            }
        }
        
        $this->showModal = true;
    }

    public function requestCancellation(int $serviceId)
    {
        $service = Service::where('user_id', auth()->id())->findOrFail($serviceId);
        $service->update([
            'cancellation_requested' => true,
            'cancellation_date' => now(),
            'cancellation_type' => 'end_of_term',
            'cancellation_reason' => 'Customer requested cancellation',
        ]);
        
        session()->flash('success', 'Cancellation requested. Your service will be cancelled at the end of the billing period.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->viewingService = null;
        $this->serverStatus = [];
    }

    public function render()
    {
        $query = Service::query()
            ->where('user_id', auth()->id())
            ->with('product')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas('product', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            });

        $services = $query->latest()->paginate(10);

        return view('livewire.client.services', [
            'services' => $services,
        ])->layout('layouts.client', ['title' => 'My Services']);
    }
}
