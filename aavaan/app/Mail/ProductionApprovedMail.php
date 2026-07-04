<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * اطلاع‌رسانی تأیید حساب تیم تولید.
 */
class ProductionApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): static
    {
        return $this
            ->subject('حساب تیم تولید شما در آوان تأیید شد ✅')
            ->view('emails.production-approved')
            ->with(['user' => $this->user]);
    }
}
