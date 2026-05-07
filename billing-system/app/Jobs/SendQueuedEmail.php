<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Email\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendQueuedEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?string $templateKey,
        public string $to,
        public ?string $subject = null,
        public ?string $body = null,
        public ?int $userId = null,
        public array $variables = [],
    ) {
        $this->onQueue('emails');
    }

    public function handle(EmailService $emailService): void
    {
        if ($this->templateKey) {
            $user = $this->userId ? User::find($this->userId) : null;

            if (!$user) {
                throw new \RuntimeException('Queued email user not found.');
            }

            $emailService->sendTemplate($this->templateKey, $user, $this->variables);
            return;
        }

        if ($this->subject === null || $this->body === null) {
            throw new \RuntimeException('Queued raw email is missing subject or body.');
        }

        $emailService->send($this->to, $this->subject, $this->body, null, $this->userId);
    }
}
