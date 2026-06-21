<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\ArtistSpecialtyController;
use App\Http\Controllers\ArtistProfilePremiumController;
use App\Http\Controllers\ArtistSubscriptionController;
use App\Http\Controllers\ProductionDashboardController;
use App\Http\Controllers\ProductionAccessController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('/payment/success', fn() => view('payment.success'))->name('payment.success');
Route::get('/payment/failed', fn() => view('payment.failed'))->name('payment.failed');

Route::get('/', [HomeController::class, 'index'])->name('home');
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
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

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

Route::middleware(['auth', 'role:artist'])
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

        // Premium profile
        Route::post('/profile-premium', [ArtistProfilePremiumController::class, 'upsert'])->name('profile-premium.update');

        Route::get('/subscription', [ArtistSubscriptionController::class, 'index'])->name('subscription');
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

        // General settings (super_admin only)
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        // Activity logs
        Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs');

        // Placeholder route for future phases
        Route::get('/placeholder', fn() => view('admin.placeholder'))->name('placeholder');

        // Future phase stubs (kept so sidebar links resolve, not 404)
        // Phase 2
        Route::get('/users', fn() => view('admin.placeholder'))->name('users.index');
        Route::get('/users/{id}', fn() => view('admin.placeholder'))->name('users.show');
        // Phase 3
        Route::get('/subscriptions', fn() => view('admin.placeholder'))->name('subscriptions.index');
        Route::get('/payments', fn() => view('admin.placeholder'))->name('payments.index');
        // Phase 4
        Route::get('/moderation', fn() => view('admin.placeholder'))->name('moderation.index');
        Route::get('/reports/violations', fn() => view('admin.placeholder'))->name('reports.violations');
        // Phase 5
        Route::get('/content', fn() => view('admin.placeholder'))->name('content.index');
        // Phase 6
        Route::get('/tickets', fn() => view('admin.placeholder'))->name('tickets.index');
        Route::get('/notifications', fn() => view('admin.placeholder'))->name('notifications.index');
        // Phase 7
        Route::get('/reports', fn() => view('admin.placeholder'))->name('reports.index');
        Route::get('/backup', fn() => view('admin.placeholder'))->name('backup.index');
    });

// ═══════════════════════════════════════════════════
// Production Dashboard
// ═══════════════════════════════════════════════════
Route::middleware(['auth', 'role:production'])
    ->prefix('dashboard/production')
    ->name('production.')
    ->group(function () {
        Route::get('/', [ProductionDashboardController::class, 'index'])->name('dashboard');
        Route::get('/search', [ProductionDashboardController::class, 'search'])->name('search');
        Route::get('/saved', [ProductionDashboardController::class, 'saved'])->name('saved');
        Route::get('/access', [ProductionAccessController::class, 'index'])->name('access');
        Route::post('/access/buy', [ProductionAccessController::class, 'buy'])->name('access.buy');
        Route::get('/access/callback', [ProductionAccessController::class, 'callback'])->name('access.callback');
        Route::post('/access/unlock', [ProductionAccessController::class, 'unlock'])->name('access.unlock');
    });
