<?php

namespace Tests\Feature;

use App\Contracts\PaymentGatewayInterface;
use App\Models\ArtistProfile;
use App\Models\DiscountCode;
use App\Models\DiscountCodeUse;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Support\Festival;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * کد تخفیف / کد جشنواره در داشبورد هنرمند — اعتبارسنجی سمت سرور + مصرف اتمیک.
 */
class DiscountCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // به‌صورت پیش‌فرض جشنواره خاموش تا مسیر پرداخت/تخفیف واقعی سنجیده شود.
        SystemSetting::set('festival_active', '0');
        Festival::forgetCache();
    }

    private function artist(): User
    {
        static $n = 0;
        $n++;
        $user = User::create([
            'name'              => "هنرمند {$n}",
            'email'             => "disc-artist{$n}@example.com",
            'password'          => 'password',
            'role'              => 'artist',
            'email_verified_at' => now(),
        ]);
        ArtistProfile::create(['user_id' => $user->id, 'field' => 'بازیگری', 'username' => "disc-artist-{$n}"]);
        return $user;
    }

    private function admin(): User
    {
        static $m = 0;
        $m++;
        return User::create([
            'name' => 'ادمین', 'email' => "disc-admin{$m}@example.com",
            'password' => 'password', 'role' => 'admin', 'email_verified_at' => now(),
        ]);
    }

    private function makeCode(array $overrides = []): DiscountCode
    {
        return DiscountCode::create(array_merge([
            'code'       => 'SAVE20',
            'type'       => 'percent',
            'value'      => 20,
            'max_uses'   => null,
            'used_count' => 0,
            'is_active'  => true,
            'created_by' => $this->admin()->id,
        ], $overrides));
    }

    /** صفحهٔ اشتراک بخش «کد تخفیف یا کد جشنواره» را رندر می‌کند (خارج از حالت جشنواره). */
    public function test_subscription_page_renders_discount_box(): void
    {
        $html = $this->actingAs($this->artist())
            ->get(route('artist.subscription'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('کد تخفیف یا کد جشنواره دارید؟', $html);
        $this->assertStringContainsString('اعمال کد', $html);
    }

    /** کد معتبر (درصدی) → قیمت جدید هر پلن محاسبه و برگردانده می‌شود. */
    public function test_valid_percent_code_returns_discounted_prices(): void
    {
        $this->makeCode(['code' => 'SAVE20', 'type' => 'percent', 'value' => 20]);
        $prices = config('aavaan.artist_subscription');

        $this->actingAs($this->artist())
            ->postJson(route('artist.subscription.discount'), ['code' => 'save20']) // حساس به بزرگی/کوچکی نباشد
            ->assertOk()
            ->assertJson([
                'valid' => true,
                'plans' => [
                    'monthly' => ['original' => (int) $prices['monthly_price'], 'final' => (int) round($prices['monthly_price'] * 0.8)],
                    'yearly'  => ['original' => (int) $prices['yearly_price'],  'final' => (int) round($prices['yearly_price'] * 0.8)],
                ],
            ]);
    }

    /** کد نامعتبر (ناموجود) → valid=false با پیام فارسی. */
    public function test_unknown_code_is_invalid(): void
    {
        $this->actingAs($this->artist())
            ->postJson(route('artist.subscription.discount'), ['code' => 'NOPE'])
            ->assertOk()
            ->assertJson(['valid' => false]);
    }

    /** کد منقضی → valid=false با پیام انقضا. */
    public function test_expired_code_is_invalid(): void
    {
        $this->makeCode(['code' => 'OLD', 'valid_until' => now()->subDay()]);

        $this->actingAs($this->artist())
            ->postJson(route('artist.subscription.discount'), ['code' => 'OLD'])
            ->assertOk()
            ->assertJson(['valid' => false, 'message' => 'این کد تخفیف منقضی شده است.']);
    }

    /** کد سقف‌پرشده → valid=false با پیام تکمیل ظرفیت. */
    public function test_maxed_out_code_is_invalid(): void
    {
        $this->makeCode(['code' => 'FULL', 'max_uses' => 2, 'used_count' => 2]);

        $this->actingAs($this->artist())
            ->postJson(route('artist.subscription.discount'), ['code' => 'FULL'])
            ->assertOk()
            ->assertJson(['valid' => false, 'message' => 'ظرفیت استفاده از این کد تخفیف تکمیل شده است.']);
    }

    /** اعمال کد در جشنواره → پیام روشن + کد سوزانده نمی‌شود. */
    public function test_applying_code_during_festival_does_not_burn_it(): void
    {
        SystemSetting::set('festival_active', '1');
        SystemSetting::set('festival_ends_at', '2026-09-22');
        Festival::forgetCache();

        $code = $this->makeCode(['code' => 'FEST', 'max_uses' => 5, 'used_count' => 0]);

        $this->actingAs($this->artist())
            ->postJson(route('artist.subscription.discount'), ['code' => 'FEST'])
            ->assertOk()
            ->assertJson(['valid' => true, 'festival_active' => true]);

        // کد نباید مصرف شده باشد.
        $this->assertSame(0, $code->fresh()->used_count);
        $this->assertSame(0, DiscountCodeUse::count());
    }

    /** مصرف اتمیک: پرداخت موفق با کد → used_count افزایش + ثبت DiscountCodeUse. */
    public function test_successful_payment_consumes_discount_code(): void
    {
        $this->bindFakeGateway();
        $code = $this->makeCode(['code' => 'PAY10', 'type' => 'percent', 'value' => 10, 'max_uses' => 3]);
        $artist = $this->artist();

        // آغاز پرداخت با کد → به درگاه (fake) هدایت می‌شود.
        $this->actingAs($artist)->post(route('artist.subscription.pay'), [
            'plan'          => 'monthly',
            'discount_code' => 'PAY10',
        ])->assertRedirect();

        $payment = \App\Models\Payment::latest('id')->first();
        $prices  = config('aavaan.artist_subscription');
        $this->assertSame((int) round($prices['monthly_price'] * 0.9), $payment->amount);

        // بازگشت از درگاه با وضعیت موفق → مصرف کد.
        $this->actingAs($artist)->get(route('artist.subscription.callback', ['payment' => $payment->id, 'Status' => 'OK']));

        $this->assertSame(1, $code->fresh()->used_count);
        $this->assertSame(1, DiscountCodeUse::where('discount_code_id', $code->id)->count());
    }

    /** مصرف اتمیک هرگز از سقف فراتر نمی‌رود (بدون oversell). */
    public function test_consume_atomically_respects_max_uses(): void
    {
        $code = $this->makeCode(['code' => 'CAP', 'max_uses' => 1, 'used_count' => 0]);

        $this->assertTrue(DiscountCode::consumeAtomically($code->id));
        $this->assertFalse(DiscountCode::consumeAtomically($code->id)); // سقف پر شده
        $this->assertSame(1, $code->fresh()->used_count);
    }

    /** درگاه پرداخت جعلی برای تست جریان پرداخت بدون تماس واقعی. */
    private function bindFakeGateway(): void
    {
        $this->app->instance(PaymentGatewayInterface::class, new class implements PaymentGatewayInterface {
            public function initiate(int $amountTomans, string $description, string $callbackUrl): array
            {
                return ['authority' => 'FAKE-AUTH', 'redirect_url' => $callbackUrl];
            }

            public function verify(string $authority, int $amountTomans): string
            {
                return 'FAKE-REF-123';
            }
        });
    }
}
