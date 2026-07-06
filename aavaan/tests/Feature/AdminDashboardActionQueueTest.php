<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\ArtistSpecialty;
use App\Models\SpecialtyCategory;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * صف اقدامات داشبورد ادمین (buildActionQueue).
 *
 * پوشش سه حالت ورودی به merge():
 *   ۱) هیچ تیم pending + یک Verification تخصص pending  ← همان سناریوی باگ getKey() on array
 *   ۲) هر دو موجود
 *   ۳) هر دو خالی
 * در هر سه حالت GET /admin با کاربر ادمین باید status 200 برگرداند.
 */
class AdminDashboardActionQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
        $this->seed(\Database\Seeders\SpecialtyCategoriesSeeder::class);
        $this->seed(\Database\Seeders\SpecialtyAttributeDefinitionsSeeder::class);
        $this->seed(\Database\Seeders\SpecialtySubcategoriesSeeder::class);
    }

    private function leaf(): SpecialtyCategory
    {
        return SpecialtyCategory::where('parent_id', 1)->orderBy('id')->firstOrFail();
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'ادمین', 'email' => 'adm'.rand(1, 99999).'@x.com',
            'password' => 'password', 'role' => 'admin',
        ]);
    }

    /** یک Verification تخصص pending می‌سازد (بدون هیچ تیم production). */
    private function makePendingSpecialtyVerification(): Verification
    {
        static $n = 0; $n++;
        $user = User::create([
            'name' => "هنرمند {$n}", 'email' => "art{$n}@x.com",
            'password' => 'password', 'role' => 'artist', 'email_verified_at' => now(),
        ]);
        ArtistProfile::create(['user_id' => $user->id, 'field' => 'بازیگری', 'is_active' => true, 'username' => "art-{$n}"]);
        $spec = ArtistSpecialty::create([
            'user_id' => $user->id, 'category_id' => $this->leaf()->id,
            'is_primary' => true, 'attributes' => [],
        ]);

        return Verification::create([
            'user_id' => $user->id, 'artist_specialty_id' => $spec->id,
            'type' => 'specialty', 'status' => 'pending', 'artist_note' => 'x',
        ]);
    }

    private function makePendingProductionTeam(): User
    {
        static $n = 0; $n++;

        return User::create([
            'name' => "تیم {$n}", 'email' => "pend{$n}@x.com",
            'password' => 'password', 'role' => 'production', 'approval_status' => 'pending',
        ]);
    }

    /**
     * حالت ۱ — سناریوی باگ: هیچ تیم pending، ولی یک Verification تخصص pending.
     * پیش از فیکس، merge روی Eloquent Collection خالیِ تیم‌ها با آرایه‌ها
     * خطای «Call to a member function getKey() on array» می‌دهد.
     */
    public function test_action_queue_with_only_pending_verification_returns_200(): void
    {
        $this->makePendingSpecialtyVerification();

        $res = $this->actingAs($this->admin())->get('/admin')->assertOk();
        $this->assertNotEmpty($res->viewData('actionQueue'));
    }

    /** حالت ۲ — هر دو موجود. */
    public function test_action_queue_with_both_pending_returns_200(): void
    {
        $this->makePendingSpecialtyVerification();
        $this->makePendingProductionTeam();

        $res = $this->actingAs($this->admin())->get('/admin')->assertOk();
        $this->assertCount(2, $res->viewData('actionQueue'));
    }

    /** حالت ۳ — هر دو خالی. */
    public function test_action_queue_with_nothing_pending_returns_200(): void
    {
        $res = $this->actingAs($this->admin())->get('/admin')->assertOk();
        $this->assertEmpty($res->viewData('actionQueue'));
    }
}
