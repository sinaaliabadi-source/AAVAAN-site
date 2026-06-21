<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    use LogsAdminActivity;

    private array $fields = [
        'contact_phone',
        'contact_email',
        'contact_address',
        'social_instagram',
        'social_telegram',
        'social_linkedin',
        'social_twitter',
        'seo_default_title',
        'seo_default_description',
        'platform_commission_rate',
    ];

    public function index()
    {
        $settings = [];
        foreach ($this->fields as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'contact_phone'           => 'nullable|string|max:20',
            'contact_email'           => 'nullable|email|max:100',
            'contact_address'         => 'nullable|string|max:500',
            'social_instagram'        => 'nullable|url|max:200',
            'social_telegram'         => 'nullable|string|max:200',
            'social_linkedin'         => 'nullable|url|max:200',
            'social_twitter'          => 'nullable|url|max:200',
            'seo_default_title'       => 'nullable|string|max:80',
            'seo_default_description' => 'nullable|string|max:200',
            'platform_commission_rate'=> 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($this->fields as $key) {
            $type = $key === 'platform_commission_rate' ? 'integer' : 'string';
            Setting::set($key, $validated[$key] ?? '', $type);
        }

        $this->logAdminActivity('settings_updated', 'تنظیمات عمومی سیستم به‌روزرسانی شد.');

        return back()->with('success', 'تنظیمات با موفقیت ذخیره شد.');
    }
}
