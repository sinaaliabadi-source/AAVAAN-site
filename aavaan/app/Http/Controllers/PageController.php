<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\ContactMessage;
use App\Models\SpecialtyCategory;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        $categories = SpecialtyCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
        return view('pages.about', compact('categories'));
    }

    public function howItWorks() { return view('pages.how-it-works'); }

    public function artists()
    {
        // منبع واحد حوزه‌های فعالیت: config/art_fields.php
        $artFields     = config('art_fields');
        $categoryCount = count($artFields);
        return view('pages.artists', compact('categoryCount', 'artFields'));
    }

    public function production() { return view('pages.production'); }
    public function terms()    { return view('pages.terms'); }
    public function privacy()  { return view('pages.privacy'); }

    public function pricing()
    {
        $monthlyPrice = config('aavaan.artist_subscription.monthly_price');
        $yearlyPrice  = config('aavaan.artist_subscription.yearly_price');
        $singlePrice  = config('aavaan.production_access.single_price');
        $bundle5Price = config('aavaan.production_access.bundle_5_price');
        $bundle10Price = config('aavaan.production_access.bundle_10_price');

        return view('pages.pricing', compact(
            'monthlyPrice', 'yearlyPrice', 'singlePrice', 'bundle5Price', 'bundle10Price'
        ));
    }

    public function faq()
    {
        $faqs = Faq::active()->get()->groupBy('category');
        return view('pages.faq', compact('faqs'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:200',
            'phone'   => 'nullable|string|max:15',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'پیام شما دریافت شد. به زودی با شما تماس می‌گیریم.');
    }
}
