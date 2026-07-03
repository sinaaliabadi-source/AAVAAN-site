<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use Illuminate\Http\Request;

class CmsCategoryController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $categories = CmsCategory::withCount('posts')->with('parent')->orderBy('sort_order')->get();
        return view('admin.cms.categories.index', compact('categories'));
    }

    public function create()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.cms.categories.form', [
            'category' => new CmsCategory(),
            'parents'  => CmsCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $data = $this->validateData($request);
        CmsCategory::create($data);

        return redirect()->route('admin.cms.categories.index')->with('success', 'دسته‌بندی ایجاد شد.');
    }

    public function edit(CmsCategory $category)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.cms.categories.form', [
            'category' => $category,
            'parents'  => CmsCategory::where('id', '!=', $category->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, CmsCategory $category)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $data = $this->validateData($request, $category->id);
        $category->update($data);

        return redirect()->route('admin.cms.categories.index')->with('success', 'دسته‌بندی به‌روزرسانی شد.');
    }

    public function destroy(CmsCategory $category)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $category->delete();

        return back()->with('success', 'دسته‌بندی حذف شد.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'nullable|string|max:120',
            'description' => 'nullable|string|max:1000',
            'parent_id'   => 'nullable|integer|exists:cms_categories,id',
            'color'       => 'nullable|string|max:7',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['slug'] = CmsCategory::uniqueSlug($request->filled('slug') ? $request->slug : $request->name, $ignoreId);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
