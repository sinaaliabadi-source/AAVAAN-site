<?php

namespace Tests\Feature;

use App\Models\CmsCategory;
use App\Models\CmsPost;
use App\Models\CmsTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(\Database\Seeders\CmsSeeder::class);
    }

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@ex.com'],
            ['name' => 'ادمین', 'password' => 'password', 'role' => 'admin'],
        );
    }

    public function test_public_blog_index_returns_200_and_lists_posts(): void
    {
        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('مجله آوان')
            ->assertSee('چگونه یک پروفایل کستینگ حرفه‌ای بسازیم؟');
    }

    public function test_blog_show_increments_view_count_and_shows_content(): void
    {
        $post = CmsPost::published()->first();
        $before = $post->view_count;

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee($post->title);

        $this->assertSame($before + 1, $post->fresh()->view_count);
    }

    public function test_blog_category_and_tag_pages_work(): void
    {
        $cat = CmsCategory::first();
        $this->get(route('blog.category', $cat->slug))->assertOk();

        $tag = CmsTag::first();
        $this->get(route('blog.tag', $tag->slug))->assertOk();
    }

    public function test_draft_post_is_not_publicly_visible(): void
    {
        $draft = CmsPost::create([
            'title' => 'پیش‌نویس مخفی', 'slug' => 'hidden-draft',
            'content' => '<p>محتوای پیش‌نویس</p>', 'status' => 'draft',
        ]);

        $this->get(route('blog.show', $draft->slug))->assertNotFound();
    }

    public function test_faq_page_renders_from_cms(): void
    {
        // ماژول هنرباز به‌صورت پیش‌فرض پنهان است؛ دستهٔ سوالات هنرباز نباید نمایش داده شود.
        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('سوالات متداول')
            ->assertDontSee('هنرباز چیست؟');
    }

    public function test_faq_page_shows_honarbaz_category_when_enabled(): void
    {
        // در محیط واقعی، فلگ روشن یعنی routeهای هنرباز هم register شده‌اند
        // (هدر به route('honarbaz.landing') لینک می‌دهد)، پس هر دو را همگام می‌کنیم.
        config(['honarbaz.enabled' => true]);
        $this->app['router']->middleware('web')->group(base_path('routes/web.php'));
        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('هنرباز چیست؟');
    }

    public function test_terms_and_privacy_render_from_cms(): void
    {
        $this->get(route('terms'))->assertOk()->assertSee('شرایط استفاده');
        $this->get(route('privacy'))->assertOk()->assertSee('حریم خصوصی');
    }

    public function test_reading_time_is_calculated(): void
    {
        $this->assertSame(1, CmsPost::calculateReadingTime('<p>یک دو سه</p>'));
        $long = str_repeat('کلمه ', 500);
        $this->assertGreaterThanOrEqual(2, CmsPost::calculateReadingTime($long));
    }

    public function test_admin_can_create_post_with_auto_slug_and_tags(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cms.posts.store'), [
                'title'   => 'مقالهٔ آزمایشی جدید',
                'content' => '<p>' . str_repeat('محتوا ', 60) . '</p>',
                'status'  => 'published',
                'tags'    => ['برچسب یک', 'برچسب دو'],
            ])->assertRedirect();

        $post = CmsPost::where('title', 'مقالهٔ آزمایشی جدید')->first();
        $this->assertNotNull($post);
        $this->assertNotEmpty($post->slug);
        $this->assertNotNull($post->published_at);
        $this->assertGreaterThanOrEqual(1, $post->reading_time);
        $this->assertCount(2, $post->tags);
    }

    public function test_admin_publish_unpublish_toggles_status(): void
    {
        $post = CmsPost::create([
            'title' => 'برای انتشار', 'slug' => 'to-publish',
            'content' => '<p>x</p>', 'status' => 'draft',
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.cms.posts.publish', $post))->assertRedirect();
        $this->assertSame('published', $post->fresh()->status);
        $this->assertNotNull($post->fresh()->published_at);

        $this->actingAs($admin)->post(route('admin.cms.posts.unpublish', $post))->assertRedirect();
        $this->assertSame('draft', $post->fresh()->status);
    }

    public function test_media_upload_stores_file_and_record(): void
    {
        $res = $this->actingAs($this->admin())
            ->post(route('admin.cms.media.upload'), [
                'file' => UploadedFile::fake()->image('pic.jpg', 800, 600),
            ]);

        $res->assertOk()->assertJsonStructure(['id', 'url', 'name']);
        $media = \App\Models\CmsMedia::first();
        $this->assertNotNull($media);
        $full = public_path($media->file_path);
        $this->assertFileExists($full);
        @unlink($full);
    }

    public function test_non_admin_cannot_access_cms_admin(): void
    {
        $user = User::create(['name' => 'u', 'email' => 'u@ex.com', 'password' => 'password', 'role' => 'artist']);
        $this->actingAs($user)->get(route('admin.cms.posts.index'))->assertForbidden();
    }

    public function test_admin_cms_screens_render(): void
    {
        $admin = $this->admin();
        $post  = CmsPost::published()->first();

        $this->actingAs($admin)->get(route('admin.cms.posts.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.posts.create'))->assertOk()->assertSee('editor-container', false);
        $this->actingAs($admin)->get(route('admin.cms.posts.edit', $post))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.tags.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.pages.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.pages.edit', 'terms'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.faqs.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cms.media.index'))->assertOk();
    }
}
