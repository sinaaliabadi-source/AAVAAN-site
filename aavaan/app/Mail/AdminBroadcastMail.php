<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailBody,
    ) {}

    public function build(): static
    {
        return $this
            ->subject($this->emailSubject)
            ->view('emails.admin-broadcast')
            ->with([
                'emailSubject' => $this->emailSubject,
                'emailBody'    => $this->emailBody,
            ]);
    }
}
