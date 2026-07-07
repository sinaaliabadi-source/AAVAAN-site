<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * قالب ایمیل «فعال‌سازی حساب»: دکمه باید استایل inline کامل داشته باشد و آدرس
 * امضاشده در href سالم بماند (بدون تبدیل & به &amp; که امضا را خراب می‌کند).
 */
class VerifyEmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function renderVerifyEmail(): string
    {
        $user = User::create([
            'name'              => 'هنرمند تست',
            'email'             => 'verify-tpl@example.com',
            'password'          => 'password',
            'role'              => 'artist',
            'email_verified_at' => null,
        ]);

        $mailMessage = (new VerifyEmailNotification())->toMail($user);

        // render() خروجی HTML را به‌صورت رشته برمی‌گرداند
        return (string) $mailMessage->render();
    }

    /** دکمهٔ فعال‌سازی استایل inline کامل دارد (مستقل از کلاس .btn که Gmail حذف می‌کند). */
    public function test_button_has_inline_styles(): void
    {
        $html = $this->renderVerifyEmail();

        $this->assertStringContainsString('فعال‌سازی حساب', $html);
        // رنگ پس‌زمینهٔ طلایی و padding باید مستقیماً روی <a> باشند
        $this->assertMatchesRegularExpression(
            '/<a[^>]+style="[^"]*background:#C9A24B[^"]*padding:14px 40px/s',
            $html
        );
    }

    /** آدرس امضاشده در href دکمه سالم است: & به &amp; تبدیل نشده و پارامترها موجودند. */
    public function test_signed_url_in_href_is_not_entity_encoded(): void
    {
        $html = $this->renderVerifyEmail();

        // استخراج href دکمه
        $this->assertMatchesRegularExpression('/<a\s+href="([^"]*verify[^"]*)"/', $html, 'دکمهٔ تأیید یافت نشد');
        preg_match('/<a\s+href="([^"]*verify[^"]*)"/', $html, $m);
        $href = $m[1];

        $this->assertStringContainsString('expires=', $href);
        $this->assertStringContainsString('signature=', $href);
        // نباید در href علامت & به صورت انتیتی &amp; باشد
        $this->assertStringNotContainsString('&amp;', $href);
        $this->assertStringContainsString('&signature=', $href);
    }
}
