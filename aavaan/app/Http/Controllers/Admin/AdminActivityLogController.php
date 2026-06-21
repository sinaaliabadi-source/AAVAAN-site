<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;

class AdminActivityLogController extends Controller
{
    public function index()
    {
        $logs = AdminActivityLog::with('admin')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
