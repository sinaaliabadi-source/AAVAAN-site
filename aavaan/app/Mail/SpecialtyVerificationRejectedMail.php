<?php

namespace App\Mail;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * اطلاع‌رسانی رد درخواست تأیید تخصص همراه با دلیل.
 */
class SpecialtyVerificationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Verification $verification) {}

    public function build(): static
    {
        return $this
            ->subject('نتیجهٔ بررسی تأیید تخصص در آوان')
            ->view('emails.verification-rejected')
            ->with([
                'verification' => $this->verification,
                'categoryName' => $this->verification->artistSpecialty?->category?->name_fa ?? 'تخصص',
                'reason'       => $this->verification->notes,
            ]);
    }
}
