<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use App\Models\CmsPost;
use App\Models\CmsTag;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class CmsPostController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $query = CmsPost::with(['author', 'category']);

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category_id', (int) $request->category);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%"));
        }

        $posts      = $query->latest()->paginate(20)->withQueryString();
        $categories = CmsCategory::orderBy('sort_order')->get();

        return view('admin.cms.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.cms.posts.form', [
            'post'       => new CmsPost(['status' => 'draft']),
            'categories' => CmsCategory::orderBy('sort_order')->get(),
            'allTags'    => CmsTag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $data = $this->validateData($request);
        $post = new CmsPost($data);
        $post->author_id   = auth()->id();
        $post->slug        = CmsPost::uniqueSlug($request->filled('slug') ? $request->slug : $request->title);
        $post->reading_time = CmsPost::calculateReadingTime($request->content);
        $this->applyPublishState($post, $request);
        $post->save();

        $this->syncTags($post, $request->input('tags', []));

        $this->logAdminActivity('cms_post_created', "مقالهٔ «{$post->title}» ایجاد شد.", 'cms_post', $post->id);

        return redirect()->route('admin.cms.posts.edit', $post)->with('success', 'مقاله ذخیره شد.');
    }

    public function edit(CmsPost $post)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $post->load('tags');

        return view('admin.cms.posts.form', [
            'post'       => $post,
            'categories' => CmsCategory::orderBy('sort_order')->get(),
            'allTags'    => CmsTag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, CmsPost $post)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $data = $this->validateData($request);
        $post->fill($data);
        if ($request->filled('slug')) {
            $post->slug = CmsPost::uniqueSlug($request->slug, $post->id);
        }
        $post->reading_time = CmsPost::calculateReadingTime($request->content);
        $this->applyPublishState($post, $request);
        $post->save();

        $this->syncTags($post, $request->input('tags', []));

        return redirect()->route('admin.cms.posts.edit', $post)->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(CmsPost $post)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $post->delete();

        return redirect()->route('admin.cms.posts.index')->with('success', 'مقاله حذف شد.');
    }

    public function publish(CmsPost $post)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $post->update([
            'status'       => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        return back()->with('success', 'مقاله منتشر شد.');
    }

    public function unpublish(CmsPost $post)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $post->update(['status' => 'draft']);

        return back()->with('success', 'مقاله به پیش‌نویس بازگشت.');
    }

    public function preview(CmsPost $post)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $post->load(['author', 'category', 'tags']);
        $related = collect();

        return view('blog.show', compact('post', 'related'));
    }

    // ─────────────────────────── helpers ───────────────────────────

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'required|string',
            'cover_image'      => 'nullable|string|max:255',
            'category_id'      => 'nullable|integer|exists:cms_categories,id',
            'status'           => 'required|in:draft,published,archived',
            'is_featured'      => 'nullable|boolean',
            'published_at'     => 'nullable|date',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
    }

    private function applyPublishState(CmsPost $post, Request $request): void
    {
        $post->is_featured = $request->boolean('is_featured');

        if ($request->filled('published_at')) {
            $post->published_at = $request->published_at;
        } elseif ($post->status === 'published' && is_null($post->published_at)) {
            $post->published_at = now();
        }
    }

    private function syncTags(CmsPost $post, array $tagNames): void
    {
        $ids = [];
        foreach ($tagNames as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $ids[] = CmsTag::findOrCreateByName($name)->id;
        }
        $post->tags()->sync($ids);
    }
}
