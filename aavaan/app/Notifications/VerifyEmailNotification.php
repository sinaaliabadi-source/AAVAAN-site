<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * ایمیل «فعال‌سازی حساب» با قالب فارسی و برندِ آوان.
 *
 * URL امضاشدهٔ تأیید را از کلاس پایهٔ Laravel می‌گیرد (verificationUrl) و فقط
 * قالب نمایش را به Blade برندِ پروژه تغییر می‌دهد.
 */
class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('فعال‌سازی حساب شما در آوان')
            ->view('emails.verify-email', [
                'verifyUrl'  => $verifyUrl,
                'notifiable' => $notifiable,
            ]);
    }
}
