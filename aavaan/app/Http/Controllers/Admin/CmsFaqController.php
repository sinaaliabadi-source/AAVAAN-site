<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use Illuminate\Http\Request;

class CmsFaqController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $faqs = CmsFaq::orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        return view('admin.cms.faqs.index', ['groups' => $faqs, 'categories' => CmsFaq::CATEGORIES]);
    }

    public function create()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.cms.faqs.form', ['faq' => new CmsFaq(['category' => 'general']), 'categories' => CmsFaq::CATEGORIES]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        CmsFaq::create($this->validateData($request));
        return redirect()->route('admin.cms.faqs.index')->with('success', 'سوال ذخیره شد.');
    }

    public function edit(CmsFaq $faq)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.cms.faqs.form', ['faq' => $faq, 'categories' => CmsFaq::CATEGORIES]);
    }

    public function update(Request $request, CmsFaq $faq)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $faq->update($this->validateData($request));
        return redirect()->route('admin.cms.faqs.index')->with('success', 'سوال به‌روزرسانی شد.');
    }

    public function destroy(CmsFaq $faq)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $faq->delete();
        return back()->with('success', 'سوال حذف شد.');
    }

    /** به‌روزرسانی ترتیب و/یا وضعیت نمایش با AJAX. */
    public function reorder(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        // toggle سریع وضعیت نمایش
        if ($request->filled('toggle_id')) {
            $faq = CmsFaq::find($request->toggle_id);
            if ($faq) {
                $faq->update(['is_published' => ! $faq->is_published]);
            }
            return back()->with('success', 'وضعیت نمایش تغییر کرد.');
        }

        // ترتیب جدید: آرایه‌ای از شناسه‌ها
        foreach ((array) $request->input('order', []) as $index => $id) {
            CmsFaq::where('id', (int) $id)->update(['sort_order' => $index]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'ترتیب ذخیره شد.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'question'    => 'required|string|max:500',
            'answer'      => 'required|string|max:5000',
            'category'    => 'required|in:artist,production,payment,general,honarbaz',
            'sort_order'  => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);
        $data['is_published'] = $request->boolean('is_published', true);
        $data['sort_order']   = (int) ($data['sort_order'] ?? 0);
        return $data;
    }
}
