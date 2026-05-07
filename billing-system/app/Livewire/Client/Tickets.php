<?php

namespace App\Livewire\Client;

use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Services\Email\EmailService;
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
    public bool $showCreateModal = false;
    public bool $showViewModal = false;
    public ?Ticket $viewingTicket = null;
    public string $replyMessage = '';
    public array $replyAttachments = [];

    // Create form
    public string $subject = '';
    public string $message = '';
    public ?int $departmentId = null;
    public ?int $serviceId = null;
    public string $priority = 'medium';
    public array $attachments = [];

    protected $queryString = ['search', 'status'];

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->subject = '';
        $this->message = '';
        $this->departmentId = null;
        $this->serviceId = null;
        $this->priority = 'medium';
        $this->replyMessage = '';
        $this->attachments = [];
        $this->replyAttachments = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createTicket()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'departmentId' => 'required|exists:ticket_departments,id',
            'priority' => 'required|in:low,medium,high,critical',
            'attachments.*' => 'file|max:10240',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'department_id' => $this->departmentId,
            'service_id' => $this->serviceId,
            'subject' => $this->subject,
            'priority' => $this->priority,
            'status' => 'open',
        ]);

        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_staff_reply' => false,
            'is_internal_note' => false,
        ]);

        $this->storeAttachments($ticket, $ticket->replies()->latest()->first(), $this->attachments);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return;
        }

        app(EmailService::class)->sendTemplate('ticket_reply', $user, [
            'name' => $user->getFullName(),
            'ticket_number' => $ticket->ticket_number,
            'ticket_subject' => $ticket->subject,
            'reply_message' => $this->message,
            'ticket_url' => route('client.tickets'),
        ]);

        $this->resetForm();
        $this->showCreateModal = false;
        session()->flash('success', 'Ticket created successfully.');
    }

    public function viewTicket(int $ticketId)
    {
        $this->viewingTicket = Ticket::with(['replies.user', 'replies.attachments', 'department'])
            ->where('user_id', Auth::id())
            ->findOrFail($ticketId);
        $this->showViewModal = true;
    }

    public function postReply()
    {
        if (empty($this->replyMessage) || !$this->viewingTicket) {
            return;
        }

        $this->validate([
            'replyMessage' => 'required|string',
            'replyAttachments.*' => 'file|max:10240',
        ]);

        $reply = $this->viewingTicket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $this->replyMessage,
            'is_staff_reply' => false,
            'is_internal_note' => false,
        ]);

        $this->storeAttachments($this->viewingTicket, $reply, $this->replyAttachments);

        $this->viewingTicket->update(['status' => 'customer_reply']);
        
        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->viewingTicket = Ticket::with(['replies.user', 'department'])->find($this->viewingTicket->id);
        
        session()->flash('success', 'Reply posted.');
    }

    protected function storeAttachments(Ticket $ticket, $reply, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('tickets/' . $ticket->id, 'public');

            $reply->attachments()->create([
                'ticket_id' => $ticket->id,
                'filename' => basename($path),
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'path' => $path,
            ]);
        }
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showViewModal = false;
        $this->viewingTicket = null;
        $this->resetForm();
    }

    public function render()
    {
        $query = Ticket::query()
            ->where('user_id', Auth::id())
            ->with(['department', 'replies.attachments', 'replies.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ticket_number', 'like', "%{$this->search}%")
                        ->orWhere('subject', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            });

        $tickets = $query->latest()->paginate(10);
        $departments = TicketDepartment::where('is_active', true)->orderBy('name')->get();
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $services = $user ? $user->services()->whereIn('status', ['active', 'suspended'])->get() : collect();

        return view('livewire.client.tickets', [
            'tickets' => $tickets,
            'departments' => $departments,
            'services' => $services,
        ]);
    }
}
