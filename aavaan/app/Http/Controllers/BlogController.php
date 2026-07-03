<?php

namespace App\Http\Controllers;

use App\Models\CmsCategory;
use App\Models\CmsPost;
use App\Models\CmsTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = CmsPost::published()->with(['author', 'category']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        // مقالهٔ featured فقط در صفحهٔ اول و بدون فیلتر نمایش داده می‌شود.
        $featured = null;
        if (! $request->hasAny(['category', 'tag', 'page'])) {
            $featured = CmsPost::published()->where('is_featured', true)
                ->with(['author', 'category'])
                ->latest('published_at')
                ->first();
            if ($featured) {
                $query->where('id', '!=', $featured->id);
            }
        }

        $posts = $query->latest('published_at')->paginate(9)->withQueryString();

        return view('blog.index', [
            'posts'          => $posts,
            'featured'       => $featured,
            'categories'     => $this->sidebarCategories(),
            'popularTags'    => $this->popularTags(),
            'activeCategory' => $request->category,
            'activeTag'      => $request->tag,
        ]);
    }

    public function category(string $slug)
    {
        $category = CmsCategory::where('slug', $slug)->firstOrFail();

        $posts = CmsPost::published()
            ->where('category_id', $category->id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', [
            'posts'          => $posts,
            'featured'       => null,
            'categories'     => $this->sidebarCategories(),
            'popularTags'    => $this->popularTags(),
            'activeCategory' => $slug,
            'activeTag'      => null,
            'pageHeading'    => 'دستهٔ: ' . $category->name,
        ]);
    }

    public function tag(string $slug)
    {
        $tag = CmsTag::where('slug', $slug)->firstOrFail();

        $posts = CmsPost::published()
            ->whereHas('tags', fn ($q) => $q->where('cms_tags.id', $tag->id))
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', [
            'posts'          => $posts,
            'featured'       => null,
            'categories'     => $this->sidebarCategories(),
            'popularTags'    => $this->popularTags(),
            'activeCategory' => null,
            'activeTag'      => $slug,
            'pageHeading'    => 'برچسب: ' . $tag->name,
        ]);
    }

    public function show(string $slug)
    {
        $post = CmsPost::published()->with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('view_count');

        $related = CmsPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }

    private function sidebarCategories()
    {
        return CmsCategory::withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();
    }

    private function popularTags()
    {
        return CmsTag::withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(15)
            ->get();
    }
}
