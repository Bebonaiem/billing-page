<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Component;
use Livewire\WithPagination;

class Tickets extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $status = 'all';
    public ?int $departmentId = null;
    public bool $showModal = false;
    public ?Ticket $editingTicket = null;
    public string $replyMessage = '';
    public bool $isInternalNote = false;
    public array $replyAttachments = [];

    protected $queryString = ['search', 'status', 'departmentId'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewTicket(int $ticketId)
    {
        $this->editingTicket = Ticket::with(['user', 'department', 'replies.user', 'replies.attachments', 'assigned'])->findOrFail($ticketId);
        $this->showModal = true;
    }

    public function postReply()
    {
        if (empty($this->replyMessage) || !$this->editingTicket) {
            return;
        }

        $this->validate([
            'replyMessage' => 'required|string',
            'replyAttachments.*' => 'file|max:10240',
        ]);

        $reply = $this->editingTicket->reply($this->replyMessage, true, $this->isInternalNote);

        foreach ($this->replyAttachments as $file) {
            $path = $file->store('tickets/' . $this->editingTicket->id, 'public');

            $reply->attachments()->create([
                'ticket_id' => $this->editingTicket->id,
                'filename' => basename($path),
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'path' => $path,
            ]);
        }
        
        // If not internal note, update ticket status
        if (!$this->isInternalNote) {
            $this->editingTicket->update(['status' => 'answered']);
        }

        $this->replyMessage = '';
        $this->isInternalNote = false;
        $this->replyAttachments = [];
        
        // Refresh ticket data
        $this->editingTicket = Ticket::with(['user', 'department', 'replies.user', 'replies.attachments', 'assigned'])->find($this->editingTicket->id);
        
        session()->flash('success', 'Reply posted successfully.');
    }

    public function closeTicket()
    {
        if ($this->editingTicket) {
            $this->editingTicket->close();
            session()->flash('success', 'Ticket closed.');
        }
    }

    public function assignToMe()
    {
        if ($this->editingTicket) {
            $this->editingTicket->update(['assigned_to' => Auth::id()]);
            $this->editingTicket = Ticket::with(['user', 'department', 'replies.user', 'replies.attachments', 'assigned'])->find($this->editingTicket->id);
            session()->flash('success', 'Ticket assigned to you.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingTicket = null;
        $this->replyMessage = '';
        $this->isInternalNote = false;
        $this->replyAttachments = [];
    }

    public function render()
    {
        $query = Ticket::query()
            ->with(['user', 'department'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ticket_number', 'like', "%{$this->search}%")
                        ->orWhere('subject', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('email', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->departmentId, function ($query) {
                $query->where('department_id', $this->departmentId);
            });

        $tickets = $query->latest()->paginate(15);
        $departments = TicketDepartment::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.tickets', [
            'tickets' => $tickets,
            'departments' => $departments,
        ]);
    }
}
