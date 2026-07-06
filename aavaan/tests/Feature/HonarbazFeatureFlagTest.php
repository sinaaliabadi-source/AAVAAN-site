<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * فلگ نمایش ماژول هنرباز — config('honarbaz.enabled').
 *
 * پیش‌فرض فلگ خاموش است؛ باید:
 *  - routeهای عمومی هنرباز register نشوند و 404 بدهند،
 *  - هیچ لینک «هنرباز» در هدر/صفحهٔ اصلی نباشد،
 *  - routeهای گروه admin هنرباز همچنان در دسترس ادمین باشند.
 * با روشن‌کردن فلگ (در runtime) routeهای عمومی دوباره برمی‌گردند.
 */
class HonarbazFeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** برنامهٔ هنرباز را برای کنترلرهایی که به آن نیاز دارند می‌سازد. */
    private function seedProgram(): Program
    {
        return Program::updateOrCreate(
            ['slug' => 'honarbaz'],
            [
                'title'     => 'هنرباز',
                'status'    => 'active',
                'starts_at' => Carbon::now(),
                'ends_at'   => Carbon::now()->addMonths(6),
                'meta'      => ['registration_enabled' => true, 'voting_enabled' => true],
            ]
        );
    }

    private function admin(): User
    {
        return User::create([
            'name'              => 'مدیر',
            'email'             => 'admin@example.com',
            'password'          => 'password',
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * فلگ خاموش (پیش‌فرض) را با بارگذاری دوبارهٔ routeها روشن می‌کند تا
     * routeهای مشروط هنرباز در همین درخواست register شوند.
     */
    private function enableHonarbazRoutes(): void
    {
        config(['honarbaz.enabled' => true]);
        $this->app['router']->middleware('web')->group(base_path('routes/web.php'));
        $this->app['router']->getRoutes()->refreshNameLookups();
    }

    // ─────────────── فلگ خاموش ───────────────

    /** با فلگ خاموش، routeهای عمومی هنرباز register نشده‌اند. */
    public function test_public_honarbaz_routes_are_not_registered_when_disabled(): void
    {
        $this->assertFalse(config('honarbaz.enabled'), 'پیش‌فرض فلگ باید خاموش باشد.');

        $this->assertFalse(Route::has('honarbaz.landing'));
        $this->assertFalse(Route::has('honarbaz.register'));
        $this->assertFalse(Route::has('honarbaz.contestants'));
        $this->assertFalse(Route::has('honarbaz.vote'));
    }

    /** با فلگ خاموش، آدرس عمومی هنرباز 404 می‌دهد. */
    public function test_public_honarbaz_url_returns_404_when_disabled(): void
    {
        $this->get('/honarbaz')->assertNotFound();
        $this->get('/honarbaz/register')->assertNotFound();
        $this->get('/honarbaz/contestants')->assertNotFound();
    }

    /** با فلگ خاموش، هیچ لینک «هنرباز» در صفحهٔ اصلی/هدر دیده نمی‌شود. */
    public function test_home_page_has_no_honarbaz_link_when_disabled(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('هنرباز')
            ->assertDontSee('/honarbaz');
    }

    /** با فلگ خاموش، پنل ادمین هنرباز همچنان برای ادمین در دسترس است. */
    public function test_admin_honarbaz_routes_remain_available_when_disabled(): void
    {
        $this->seedProgram();

        $this->assertTrue(Route::has('admin.honarbaz.index'));

        $this->actingAs($this->admin())
            ->get(route('admin.honarbaz.index'))
            ->assertOk();
    }

    // ─────────────── فلگ روشن ───────────────

    /** با روشن‌کردن فلگ، route عمومی هنرباز دوباره register و در دسترس می‌شود. */
    public function test_public_honarbaz_route_returns_when_enabled(): void
    {
        $this->seedProgram();
        $this->enableHonarbazRoutes();

        $this->assertTrue(Route::has('honarbaz.landing'));

        $this->get('/honarbaz')->assertOk();
    }
}
