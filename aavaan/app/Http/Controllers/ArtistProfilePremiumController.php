<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfilePremium;
use Illuminate\Http\Request;

class ArtistProfilePremiumController extends Controller
{
    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'stage_name'                  => 'nullable|string|max:100',
            'legal_name'                  => 'nullable|string|max:100',
            'willing_to_travel'           => 'boolean',
            'willing_long_stay'           => 'boolean',
            'military_status'             => 'nullable|in:not_required,completed,exempt,active',
            'passport_status'             => 'nullable|in:valid,expired,none',
            'international_collaboration' => 'boolean',
            'completed_projects_count'    => 'nullable|integer|min:0|max:9999',
            'published_projects_count'    => 'nullable|integer|min:0|max:9999',
            'awards'                      => 'nullable|array',
            'awards.*.title'              => 'nullable|string|max:200',
            'awards.*.year'               => 'nullable|string|max:10',
            'awards.*.event'              => 'nullable|string|max:200',
            'memberships'                 => 'nullable|array',
            'memberships.*.name'          => 'nullable|string|max:200',
            'availability_status'         => 'required|in:ready,busy,available_from',
            'available_from_date'         => 'nullable|date|required_if:availability_status,available_from',
            'concurrent_capacity'         => 'nullable|integer|min:1|max:99',
            'day_rate_min'                => 'nullable|integer|min:0',
            'day_rate_max'                => 'nullable|integer|min:0',
            'show_day_rate'               => 'boolean',
            'imdb_url'                    => 'nullable|url|max:500',
            'instagram_url'               => 'nullable|url|max:500',
            'linkedin_url'                => 'nullable|url|max:500',
            'youtube_url'                 => 'nullable|url|max:500',
            'vimeo_url'                   => 'nullable|url|max:500',
            'website_url'                 => 'nullable|url|max:500',
        ], [
            'military_status.in'              => 'وضعیت سربازی نامعتبر است.',
            'passport_status.in'              => 'وضعیت گذرنامه نامعتبر است.',
            'availability_status.required'    => 'وضعیت در دسترس بودن الزامی است.',
            'available_from_date.required_if' => 'تاریخ آزاد شدن را وارد کنید.',
        ]);

        // Normalize boolean checkboxes (unchecked checkboxes are not sent in form data)
        foreach (['willing_to_travel', 'willing_long_stay', 'international_collaboration', 'show_day_rate'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        // Filter out blank award entries
        $validated['awards'] = collect($request->input('awards', []))
            ->filter(fn($a) => !empty(trim($a['title'] ?? '')))
            ->values()
            ->all();

        // Filter out blank membership entries
        $validated['memberships'] = collect($request->input('memberships', []))
            ->filter(fn($m) => !empty(trim($m['name'] ?? '')))
            ->values()
            ->all();

        ArtistProfilePremium::updateOrCreate(
            ['user_id' => auth()->id()],
            array_merge($validated, ['user_id' => auth()->id()])
        );

        return back()->with('success', 'اطلاعات حرفه‌ای پیشرفته ذخیره شد.');
    }
}
