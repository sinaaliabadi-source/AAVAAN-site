<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TalentDensityController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\SupportAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\ArtistSpecialtyController;
use App\Http\Controllers\ArtistVerificationController;
use App\Http\Controllers\ArtistProfilePremiumController;
use App\Http\Controllers\ArtistSubscriptionController;
use App\Http\Controllers\ProductionDashboardController;
use App\Http\Controllers\ProductionAccessController;
use App\Http\Controllers\HonarbazController;
use App\Http\Controllers\Admin\HonarbazAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminEmailController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminArtistController;
use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\Admin\AdminProductionController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminDiscountController;
use App\Http\Controllers\Admin\AdminSystemSettingController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\CmsPostController;
use App\Http\Controllers\Admin\CmsCategoryController;
use App\Http\Controllers\Admin\CmsTagController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CmsFaqController;
use App\Http\Controllers\Admin\CmsMediaController;
use Illuminate\Support\Facades\Route;

Route::get('/payment/success', fn() => view('payment.success'))->name('payment.success');
Route::get('/payment/failed', fn() => view('payment.failed'))->name('payment.failed');

// صفحه‌ی اصلی — دوزبانه و سئو-دوست با مسیرهای مجزا برای هر زبان.
// «/» به‌صورت پیش‌فرض فارسی است تا همه‌ی لینک‌های route('home') موجود سالم بمانند.
Route::get('/', [HomeController::class, 'index'])->defaults('locale', 'fa')->name('home');
Route::get('/fa', [HomeController::class, 'index'])->defaults('locale', 'fa')->name('home.fa');
Route::get('/en', [HomeController::class, 'index'])->defaults('locale', 'en')->name('home.en');

// API — تراکم هنرمندان فعال به تفکیک شهر (برای نقشه‌ی صفحه‌ی اصلی)
Route::get('/api/talent-density', [TalentDensityController::class, 'index'])->name('api.talent-density');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/artists', [PageController::class, 'artists'])->name('artists');
Route::get('/production', [PageController::class, 'production'])->name('production');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send')->middleware('throttle:contact');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ═══════════════════════════════════════════════════
// هنرباز — برنامه استعدادیابی کودکان (عمومی)
// ماژول موقتاً پشت فلگ config('honarbaz.enabled') پنهان شده است.
// وقتی فلگ خاموش باشد این routeها اصلاً register نمی‌شوند و 404 می‌دهند.
// توجه: routeهای گروه admin.honarbaz.* مستقل و همیشه فعال‌اند (پایین‌تر).
// ═══════════════════════════════════════════════════
if (config('honarbaz.enabled')) {
    Route::get('/honarbaz', [HonarbazController::class, 'landing'])->name('honarbaz.landing');
    Route::get('/honarbaz/register', [HonarbazController::class, 'registerForm'])->name('honarbaz.register');
    Route::post('/honarbaz/register', [HonarbazController::class, 'registerSubmit'])
        ->name('honarbaz.register.submit')->middleware('throttle:honarbaz-register');
    Route::get('/honarbaz/contestants', [HonarbazController::class, 'contestants'])->name('honarbaz.contestants');
    Route::post('/honarbaz/vote', [HonarbazController::class, 'vote'])
        ->name('honarbaz.vote')->middleware('throttle:honarbaz-vote');
    Route::post('/honarbaz/vote/verify', [HonarbazController::class, 'verifyVote'])
        ->name('honarbaz.vote.verify')->middleware('throttle:honarbaz-vote');
}
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

// امتیازدهی و نظرات هنرمند (فقط تیم تولید با دسترسی)
Route::middleware('auth')->group(function () {
    Route::post('/profile/{username}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::put('/profile/{username}/review', [ReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');
});

// ═══════════════════════════════════════════════════
// پشتیبانی و تیکتینگ (عمومی)
// ═══════════════════════════════════════════════════
Route::prefix('support')->name('support.')->group(function () {
    Route::get('/', [SupportController::class, 'index'])->name('index');

    Route::get('/new', [SupportController::class, 'create'])->name('create');
    Route::post('/new', [SupportController::class, 'store'])->name('store')->middleware('throttle:contact');

    Route::get('/track', [SupportController::class, 'track'])->name('track');
    Route::post('/track', [SupportController::class, 'trackResult'])->name('track.result');

    Route::get('/tickets', [SupportController::class, 'myTickets'])->name('tickets')->middleware('auth');
    Route::get('/tickets/{ticket}', [SupportController::class, 'show'])->name('show');
    Route::post('/tickets/{ticket}/reply', [SupportController::class, 'reply'])->name('reply');
    Route::post('/tickets/{ticket}/close', [SupportController::class, 'close'])->name('close');

    Route::get('/attachments/{attachment}/download', [SupportController::class, 'downloadAttachment'])->name('attachment.download');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'index'])->name('auth');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login')->middleware('throttle:login');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register')->middleware('throttle:register');
    Route::get('/auth/forgot', [AuthController::class, 'forgotForm'])->name('auth.forgot');
    Route::post('/auth/forgot', [AuthController::class, 'sendReset'])->name('auth.forgot.send')->middleware('throttle:forgot-password');
    Route::get('/auth/reset/{token}', [AuthController::class, 'resetForm'])->name('auth.reset');
    Route::post('/auth/reset', [AuthController::class, 'resetPassword'])->name('auth.reset.do')->middleware('throttle:forgot-password');
});

Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

// ── تأیید ایمیل (فعال‌سازی حساب هنرمند) — مکانیزم استاندارد Laravel ──
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'role:artist', 'verified'])
    ->prefix('dashboard/artist')
    ->name('artist.')
    ->group(function () {
        Route::get('/', [ArtistDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ArtistDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [ArtistDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/portfolio', [ArtistDashboardController::class, 'uploadPortfolio'])->name('portfolio.upload');
        Route::delete('/portfolio/{id}', [ArtistDashboardController::class, 'deletePortfolio'])->name('portfolio.delete');
        Route::post('/reel', [ArtistDashboardController::class, 'uploadReel'])->name('reel.upload');
        Route::delete('/reel/{id}', [ArtistDashboardController::class, 'deleteReel'])->name('reel.delete');
        Route::post('/work-history', [ArtistDashboardController::class, 'addWorkHistory'])->name('work-history.add');
        Route::delete('/work-history/{id}', [ArtistDashboardController::class, 'deleteWorkHistory'])->name('work-history.delete');

        // Specialty routes — delete media before the wildcard {specialty} routes to avoid collision
        Route::delete('/specialties/media/{media}', [ArtistSpecialtyController::class, 'deleteMedia'])->name('specialties.media.delete');
        Route::post('/specialties', [ArtistSpecialtyController::class, 'store'])->name('specialties.store');
        Route::put('/specialties/{specialty}', [ArtistSpecialtyController::class, 'update'])->name('specialties.update');
        Route::delete('/specialties/{specialty}', [ArtistSpecialtyController::class, 'destroy'])->name('specialties.destroy');
        Route::post('/specialties/{specialty}/primary', [ArtistSpecialtyController::class, 'setPrimary'])->name('specialties.primary');
        Route::post('/specialties/{specialty}/media', [ArtistSpecialtyController::class, 'uploadMedia'])->name('specialties.media.upload');
        // درخواست تأیید تخصص
        Route::post('/specialties/{specialty}/verify', [ArtistVerificationController::class, 'store'])->name('specialties.verify');

        // Premium profile
        Route::post('/profile-premium', [ArtistProfilePremiumController::class, 'upsert'])->name('profile-premium.update');

        Route::get('/subscription', [ArtistSubscriptionController::class, 'index'])->name('subscription');
        Route::post('/subscription/discount', [ArtistSubscriptionController::class, 'validateDiscount'])->name('subscription.discount');
        Route::post('/subscription/pay', [ArtistSubscriptionController::class, 'pay'])->name('subscription.pay');
        Route::get('/subscription/callback', [ArtistSubscriptionController::class, 'callback'])->name('subscription.callback');
    });

// ═══════════════════════════════════════════════════
// Admin Panel
// ═══════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Global search
        Route::get('/search', [AdminSearchController::class, 'index'])->name('search');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        // جستجوی هنرمند برای autocomplete اشتراک دستی (JSON)
        Route::get('/api/artist-search', [AdminSubscriptionController::class, 'artistSearch'])->name('api.artist-search');
        Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/restore', [AdminUserController::class, 'restore'])->name('users.restore');

        // Artists
        Route::get('/artists', [AdminArtistController::class, 'index'])->name('artists.index');
        Route::get('/artists/{id}/edit', [AdminArtistController::class, 'edit'])->name('artists.edit');
        Route::put('/artists/{id}', [AdminArtistController::class, 'update'])->name('artists.update');

        // Verifications
        Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
        Route::get('/verifications/{id}', [AdminVerificationController::class, 'show'])->name('verifications.show');
        Route::post('/verifications/{id}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{id}/reject', [AdminVerificationController::class, 'reject'])->name('verifications.reject');

        // Production teams
        Route::get('/production-teams', [AdminProductionController::class, 'index'])->name('production.index');
        Route::get('/production-teams/{id}', [AdminProductionController::class, 'show'])->name('production.show');
        Route::post('/production-teams/{id}/approve', [AdminProductionController::class, 'approve'])->name('production.approve');
        Route::post('/production-teams/{id}/reject', [AdminProductionController::class, 'reject'])->name('production.reject');
        Route::post('/production-teams/{id}/add-credit', [AdminProductionController::class, 'addCredit'])->name('production.add-credit');

        // Subscriptions
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        // create پیش از {id} تعریف می‌شود تا با route نمایش تداخل نکند.
        Route::get('/subscriptions/create', [AdminSubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions', [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::get('/subscriptions/{id}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('/subscriptions/{id}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/{id}/extend', [AdminSubscriptionController::class, 'extend'])->name('subscriptions.extend');

        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');

        // Discount codes
        Route::get('/discount-codes', [AdminDiscountController::class, 'index'])->name('discounts.index');
        Route::get('/discount-codes/create', [AdminDiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discount-codes', [AdminDiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discount-codes/generate-code', [AdminDiscountController::class, 'generateCode'])->name('discounts.generate-code');
        Route::get('/discount-codes/{id}/edit', [AdminDiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discount-codes/{id}', [AdminDiscountController::class, 'update'])->name('discounts.update');
        Route::post('/discount-codes/{id}/toggle', [AdminDiscountController::class, 'toggle'])->name('discounts.toggle');

        // Artist reviews moderation
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{id}/toggle', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');

        // Support / ticketing
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [SupportAdminController::class, 'index'])->name('index');
            Route::get('/tickets', [SupportAdminController::class, 'tickets'])->name('tickets');
            Route::get('/tickets/{ticket}', [SupportAdminController::class, 'show'])->name('show');
            Route::post('/tickets/{ticket}/reply', [SupportAdminController::class, 'reply'])->name('reply');
            Route::post('/tickets/{ticket}/status', [SupportAdminController::class, 'updateStatus'])->name('status');
            Route::post('/tickets/{ticket}/assign', [SupportAdminController::class, 'assign'])->name('assign');
            Route::post('/tickets/{ticket}/priority', [SupportAdminController::class, 'updatePriority'])->name('priority');

            Route::get('/canned', [SupportAdminController::class, 'cannedIndex'])->name('canned.index');
            Route::post('/canned', [SupportAdminController::class, 'cannedStore'])->name('canned.store');
            Route::put('/canned/{id}', [SupportAdminController::class, 'cannedUpdate'])->name('canned.update');
            Route::delete('/canned/{id}', [SupportAdminController::class, 'cannedDestroy'])->name('canned.destroy');
        });

        // CMS — مدیریت محتوا
        Route::prefix('cms')->name('cms.')->group(function () {
            // بلاگ
            Route::post('posts/{post}/publish', [CmsPostController::class, 'publish'])->name('posts.publish');
            Route::post('posts/{post}/unpublish', [CmsPostController::class, 'unpublish'])->name('posts.unpublish');
            Route::get('posts/{post}/preview', [CmsPostController::class, 'preview'])->name('posts.preview');
            Route::resource('posts', CmsPostController::class)->except(['show']);

            // دسته‌بندی‌ها
            Route::resource('categories', CmsCategoryController::class)->except(['show']);

            // برچسب‌ها
            Route::resource('tags', CmsTagController::class)->only(['index', 'store', 'destroy']);

            // صفحات استاتیک
            Route::get('pages', [CmsPageController::class, 'index'])->name('pages.index');
            Route::get('pages/{slug}/edit', [CmsPageController::class, 'edit'])->name('pages.edit');
            Route::put('pages/{slug}', [CmsPageController::class, 'update'])->name('pages.update');

            // FAQ
            Route::post('faqs/reorder', [CmsFaqController::class, 'reorder'])->name('faqs.reorder');
            Route::resource('faqs', CmsFaqController::class)->except(['show']);

            // کتابخانه رسانه
            Route::get('media', [CmsMediaController::class, 'index'])->name('media.index');
            Route::post('media/upload', [CmsMediaController::class, 'upload'])->name('media.upload');
            Route::delete('media/{id}', [CmsMediaController::class, 'destroy'])->name('media.destroy');
            Route::get('media/{id}/url', [CmsMediaController::class, 'getUrl'])->name('media.url');
        });

        // System Settings (pricing/limits)
        Route::get('/system-settings', [AdminSystemSettingController::class, 'index'])->name('system-settings.index');
        Route::put('/system-settings', [AdminSystemSettingController::class, 'update'])->name('system-settings.update');

        // General settings (contact info, social, SEO)
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        // Email / Notifications
        Route::get('/email', [AdminEmailController::class, 'index'])->name('email.index');
        Route::post('/email', [AdminEmailController::class, 'send'])->name('email.send');

        // هنرباز — مدیریت برنامه استعدادیابی
        Route::prefix('honarbaz')->name('honarbaz.')->group(function () {
            Route::get('/', [HonarbazAdminController::class, 'index'])->name('index');
            Route::get('/registrations', [HonarbazAdminController::class, 'registrations'])->name('registrations');
            Route::post('/registrations/{id}/status', [HonarbazAdminController::class, 'updateStatus'])->name('status');
            Route::get('/votes', [HonarbazAdminController::class, 'votes'])->name('votes');
            Route::get('/export', [HonarbazAdminController::class, 'export'])->name('export');
            Route::get('/settings', [HonarbazAdminController::class, 'settings'])->name('settings');
            Route::post('/settings', [HonarbazAdminController::class, 'updateSettings'])->name('settings.update');
        });

        // Activity logs
        Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs');

        // Placeholder
        Route::get('/placeholder', fn() => view('admin.placeholder'))->name('placeholder');

        // Reports
        Route::get('/reports',                                        [AdminReportController::class, 'dashboard'])->name('reports.index');
        Route::get('/reports/chart-data',                             [AdminReportController::class, 'chartData'])->name('reports.chart-data');
        Route::get('/reports/subscriptions',                          [AdminReportController::class, 'subscriptions'])->name('reports.subscriptions');
        Route::get('/reports/subscriptions/export',                   [AdminReportController::class, 'exportSubscriptions'])->name('reports.subscriptions.export');
        Route::get('/reports/production-access',                      [AdminReportController::class, 'productionAccess'])->name('reports.production-access');
        Route::get('/reports/production-access/export',               [AdminReportController::class, 'exportProductionAccess'])->name('reports.production-access.export');
        Route::get('/reports/discounts',                              [AdminReportController::class, 'discounts'])->name('reports.discounts');
        Route::get('/reports/discounts/{id}',                         [AdminReportController::class, 'discountShow'])->name('reports.discounts.show');
        Route::get('/reports/users',                                  [AdminReportController::class, 'users'])->name('reports.users');
        Route::get('/reports/export',                                 [AdminReportController::class, 'exportPage'])->name('reports.export');
        Route::get('/reports/export/download',                        [AdminReportController::class, 'exportDownload'])->name('reports.export.download');
        Route::get('/tickets', fn() => view('admin.placeholder'))->name('tickets.index');
        Route::get('/notifications', fn() => view('admin.placeholder'))->name('notifications.index');
        Route::get('/backup', fn() => view('admin.placeholder'))->name('backup.index');
    });

// ═══════════════════════════════════════════════════
// Production Dashboard
// ═══════════════════════════════════════════════════
Route::middleware(['auth', 'role:production', 'production.approved'])
    ->prefix('dashboard/production')
    ->name('production.')
    ->group(function () {
        // صفحهٔ وضعیت انتظار تأیید — خودِ این route نباید پشت middleware تأیید باشد (جلوگیری از حلقهٔ redirect).
        Route::get('/pending-approval', [ProductionDashboardController::class, 'pendingApproval'])
            ->name('pending-approval')
            ->withoutMiddleware(\App\Http\Middleware\EnsureProductionApproved::class);
        Route::get('/', [ProductionDashboardController::class, 'index'])->name('dashboard');
        Route::get('/search', [ProductionDashboardController::class, 'search'])->name('search');
        // تصویر ناشناس هنرمند برای کارت‌های قفلِ کست‌یاب (بدون افشای مسیر واقعی فایل)
        Route::get('/anon-avatar/{code}', [ProductionDashboardController::class, 'anonAvatar'])->name('anon-avatar');
        Route::get('/saved', [ProductionDashboardController::class, 'saved'])->name('saved');
        Route::get('/access', [ProductionAccessController::class, 'index'])->name('access');
        Route::post('/access/buy', [ProductionAccessController::class, 'buy'])->name('access.buy');
        Route::get('/access/callback', [ProductionAccessController::class, 'callback'])->name('access.callback');
        Route::post('/access/unlock', [ProductionAccessController::class, 'unlock'])->name('access.unlock');
    });
