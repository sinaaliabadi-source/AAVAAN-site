<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'         => User::count(),
            'total_artists'       => User::where('role', 'artist')->count(),
            'total_production'    => User::where('role', 'production')->count(),
            'active_subscriptions'=> Subscription::where('status', 'active')
                                        ->where('expires_at', '>', now())
                                        ->count(),
            'revenue_this_month'  => Payment::where('status', 'paid')
                                        ->whereMonth('paid_at', now()->month)
                                        ->whereYear('paid_at', now()->year)
                                        ->sum('amount'),
            'new_users_today'     => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [
                                        now()->startOfWeek(),
                                        now()->endOfWeek(),
                                    ])->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
