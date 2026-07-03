<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $pages = CmsPage::orderBy('title')->get();
        return view('admin.cms.pages.index', compact('pages'));
    }

    public function edit(string $slug)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $page = CmsPage::where('slug', $slug)->firstOrFail();
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, string $slug)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $page = CmsPage::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'is_published'     => 'nullable|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $data['updated_by']   = auth()->id();
        $page->update($data);

        $this->logAdminActivity('cms_page_updated', "صفحهٔ «{$page->title}» به‌روزرسانی شد.", 'cms_page', $page->id);

        return back()->with('success', 'صفحه ذخیره شد.');
    }
}
