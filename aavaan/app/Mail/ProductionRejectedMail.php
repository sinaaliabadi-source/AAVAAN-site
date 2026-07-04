<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * اطلاع‌رسانی رد حساب تیم تولید همراه با دلیل.
 */
class ProductionRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public ?string $reason = null) {}

    public function build(): static
    {
        return $this
            ->subject('نتیجهٔ بررسی حساب تیم تولید در آوان')
            ->view('emails.production-rejected')
            ->with(['user' => $this->user, 'reason' => $this->reason]);
    }
}
