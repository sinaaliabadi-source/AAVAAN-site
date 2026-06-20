<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\ArtistSubscriptionController;
use App\Http\Controllers\ProductionDashboardController;
use App\Http\Controllers\ProductionAccessController;
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
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'index'])->name('auth');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('/auth/forgot', [AuthController::class, 'forgotForm'])->name('auth.forgot');
    Route::post('/auth/forgot', [AuthController::class, 'sendReset'])->name('auth.forgot.send');
    Route::get('/auth/reset/{token}', [AuthController::class, 'resetForm'])->name('auth.reset');
    Route::post('/auth/reset', [AuthController::class, 'resetPassword'])->name('auth.reset.do');
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
        Route::get('/subscription', [ArtistSubscriptionController::class, 'index'])->name('subscription');
        Route::post('/subscription/pay', [ArtistSubscriptionController::class, 'pay'])->name('subscription.pay');
        Route::get('/subscription/callback', [ArtistSubscriptionController::class, 'callback'])->name('subscription.callback');
    });

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
