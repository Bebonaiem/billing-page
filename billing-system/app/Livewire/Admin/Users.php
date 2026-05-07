<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public bool $showModal = false;
    public ?User $editingUser = null;

    protected $queryString = ['search', 'status'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewUser(int $userId)
    {
        $this->editingUser = User::with(['orders', 'services', 'invoices'])->findOrFail($userId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingUser = null;
    }

    public function toggleStatus(int $userId)
    {
        $user = User::findOrFail($userId);
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);
        session()->flash('success', 'User status updated.');
    }

    public function render()
    {
        $query = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            });

        $users = $query->latest()->paginate(15);

        return view('livewire.admin.users', [
            'users' => $users,
        ]);
    }
}
