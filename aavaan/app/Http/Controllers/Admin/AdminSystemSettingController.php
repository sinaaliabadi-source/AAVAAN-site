<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AdminSystemSettingController extends Controller {
    use LogsAdminActivity;

    public function index() {
        abort_unless(auth()->user()->role === 'admin', 403);
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.system-settings.index', compact('settings'));
    }

    public function update(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $keys = SystemSetting::pluck('key')->toArray();
        foreach ($keys as $key) {
            if ($request->has($key)) {
                SystemSetting::set($key, $request->input($key));
            }
        }
        $this->logAdminActivity('system_settings_updated', 'تنظیمات سیستم به‌روزرسانی شد.');
        return back()->with('success', 'تنظیمات سیستم ذخیره شد.');
    }
}
