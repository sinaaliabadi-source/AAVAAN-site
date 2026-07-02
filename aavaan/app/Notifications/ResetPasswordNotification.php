<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Override the reset URL to use our custom named route (auth.reset)
     * instead of Laravel's default 'password.reset' route which doesn't exist.
     */
    protected function resetUrl($notifiable): string
    {
        return url(route('auth.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('بازیابی رمز عبور — آوان')
            ->view('emails.reset-password', [
                'resetUrl'  => $resetUrl,
                'notifiable' => $notifiable,
            ]);
    }
}
