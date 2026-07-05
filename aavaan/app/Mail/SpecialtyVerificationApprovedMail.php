<?php

namespace App\Mail;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * اطلاع‌رسانی تأیید تخصص به هنرمند.
 */
class SpecialtyVerificationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Verification $verification) {}

    public function build(): static
    {
        return $this
            ->subject('تخصص شما در آوان تأیید شد ✔')
            ->view('emails.verification-approved')
            ->with([
                'verification' => $this->verification,
                'categoryName' => $this->verification->artistSpecialty?->category?->name_fa ?? 'تخصص',
            ]);
    }
}
