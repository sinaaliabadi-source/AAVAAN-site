<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ایمیل خوش‌آمد برای حساب‌هایی که ادمین از پنل ساخته است — شامل رمز عبور اولیه.
 */
class AccountCreatedByAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $plainPassword) {}

    public function build(): static
    {
        return $this
            ->subject('حساب شما در آوان ساخته شد')
            ->view('emails.account-created')
            ->with([
                'user'          => $this->user,
                'plainPassword' => $this->plainPassword,
            ]);
    }
}
