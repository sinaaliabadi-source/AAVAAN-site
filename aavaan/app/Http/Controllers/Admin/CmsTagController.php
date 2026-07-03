<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsTag;
use Illuminate\Http\Request;

class CmsTagController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $tags = CmsTag::withCount('posts')->orderBy('name')->paginate(50);
        return view('admin.cms.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(['name' => 'required|string|max:100']);
        CmsTag::findOrCreateByName($request->name);

        return back()->with('success', 'برچسب ذخیره شد.');
    }

    public function destroy(int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $tag = CmsTag::findOrFail($id);
        $tag->posts()->detach();
        $tag->delete();

        return back()->with('success', 'برچسب حذف شد.');
    }
}
