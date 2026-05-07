<?php

namespace App\Services\Email;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Jobs\SendQueuedEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected array $templateAliases = [
        'invoice' => 'invoice_created',
        'payment_receipt' => 'invoice_paid',
        'order_confirmation' => 'order_confirmation',
        'suspension_notice' => 'service_suspended',
        'service_activated' => 'service_activated',
        'ticket_reply' => 'ticket_reply',
        'password_reset' => 'password_reset',
        'welcome' => 'welcome',
    ];

    /**
     * Send email using template
     */
    public function sendTemplate(string $templateKey, User $user, array $variables = []): bool
    {
        $templateName = $this->templateAliases[$templateKey] ?? $templateKey;
        $template = EmailTemplate::where('name', $templateName)->where('is_active', true)->first();
        
        if (!$template) {
            Log::warning("Email template not found: {$templateKey}");
            return false;
        }

        $subject = $template->parseSubject($variables);
        $body = $template->parseContent($variables);

        return $this->send($user->email, $subject, $body, $templateKey, $user->id);
    }

    /**
     * Send raw email
     */
    public function send(string $to, string $subject, string $body, ?string $templateKey = null, ?int $userId = null): bool
    {
        try {
            Mail::send([], [], function ($message) use ($to, $subject, $body) {
                $message->to($to)
                    ->subject($subject)
                    ->html($body);
            });

            // Log the email
            \App\Models\EmailLog::create([
                'user_id' => $userId,
                'template_key' => $templateKey,
                'recipient_email' => $to,
                'subject' => $subject,
                'content' => $body,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
            ]);

            // Log the failure
            \App\Models\EmailLog::create([
                'user_id' => $userId,
                'template_key' => $templateKey,
                'recipient_email' => $to,
                'subject' => $subject,
                'content' => $body,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Queue email for sending
     */
    public function queue(string $to, string $subject, string $body, ?string $templateKey = null, ?int $userId = null): void
    {
        SendQueuedEmail::dispatch($templateKey, $to, $subject, $body, $userId)->onQueue('emails');
    }

    /**
     * Queue an email template for delivery
     */
    public function queueTemplate(string $templateKey, User $user, array $variables = []): void
    {
        SendQueuedEmail::dispatch($templateKey, $user->email, null, null, $user->id, $variables)->onQueue('emails');
    }

    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(User $user): bool
    {
        return $this->sendTemplate('welcome', $user, [
            'name' => $user->getFullName(),
            'email' => $user->email,
            'login_url' => route('client.dashboard'),
        ]);
    }

    /**
     * Send invoice generated email
     */
    public function sendInvoiceEmail($invoice): bool
    {
        return $this->sendTemplate('invoice', $invoice->user, [
            'name' => $invoice->user->getFullName(),
            'invoice_number' => $invoice->invoice_number,
            'invoice_date' => $invoice->invoice_date->format('M d, Y'),
            'due_date' => $invoice->due_date->format('M d, Y'),
            'total' => number_format($invoice->total, 2),
            'balance' => number_format($invoice->balance, 2),
            'invoice_url' => route('client.invoices'),
        ]);
    }

    /**
     * Send payment receipt email
     */
    public function sendPaymentReceipt($payment): bool
    {
        return $this->sendTemplate('payment_receipt', $payment->user, [
            'name' => $payment->user->getFullName(),
            'amount' => number_format($payment->amount, 2),
            'transaction_id' => $payment->transaction_id,
            'payment_date' => $payment->processed_at?->format('M d, Y'),
            'gateway' => $payment->gateway?->display_name ?? 'Unknown',
        ]);
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation($order): bool
    {
        return $this->sendTemplate('order_confirmation', $order->user, [
            'name' => $order->user->getFullName(),
            'order_number' => $order->order_number,
            'order_date' => $order->created_at->format('M d, Y'),
            'total' => number_format($order->total, 2),
            'order_url' => route('client.dashboard'),
        ]);
    }

    /**
     * Send service activated email
     */
    public function sendServiceActivated($service): bool
    {
        return $this->sendTemplate('service_activated', $service->user, [
            'name' => $service->user->getFullName(),
            'service_name' => $service->name ?? $service->product->name,
            'activation_date' => $service->activated_at?->format('M d, Y'),
            'service_url' => route('client.services'),
        ]);
    }

    /**
     * Send suspension notice email
     */
    public function sendSuspensionNotice($service, string $reason = ''): bool
    {
        return $this->sendTemplate('suspension_notice', $service->user, [
            'name' => $service->user->getFullName(),
            'service_name' => $service->name ?? $service->product->name,
            'suspension_date' => now()->format('M d, Y'),
            'reason' => $reason,
            'support_url' => route('client.tickets'),
        ]);
    }

    /**
     * Send ticket reply notification
     */
    public function sendTicketReplyNotification($ticket, $reply): bool
    {
        return $this->sendTemplate('ticket_reply', $ticket->user, [
            'name' => $ticket->user->getFullName(),
            'ticket_number' => $ticket->ticket_number,
            'ticket_subject' => $ticket->subject,
            'reply_message' => $reply->message,
            'ticket_url' => route('client.tickets'),
        ]);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($user, string $token): bool
    {
        return $this->sendTemplate('password_reset', $user, [
            'name' => $user->getFullName(),
            'reset_url' => url(route('password.reset', ['token' => $token, 'email' => $user->email])),
            'expiry_hours' => 24,
        ]);
    }
}
