<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\CmsPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * نقشهٔ سایت استاندارد XML.
     *
     * شامل صفحات ثابت عمومی، پست‌های منتشرشدهٔ وبلاگ و پروفایل‌های عمومی هنرمندانِ
     * فعالِ دارای username. هنرمندان بدون username عمداً حذف می‌شوند تا لینک مرده تولید نشود.
     */
    public function index(): Response
    {
        // صفحات ثابت عمومی (نام route → فرکانس تغییر)
        $staticRoutes = [
            'home'         => 'daily',
            'about'        => 'monthly',
            'how-it-works' => 'monthly',
            'artists'      => 'weekly',
            'production'   => 'monthly',
            'pricing'      => 'monthly',
            'faq'          => 'monthly',
            'contact'      => 'yearly',
            'terms'        => 'yearly',
            'privacy'      => 'yearly',
            'blog'         => 'daily',
        ];

        $urls = [];
        foreach ($staticRoutes as $name => $freq) {
            $urls[] = [
                'loc'        => route($name),
                'changefreq' => $freq,
                'lastmod'    => null,
            ];
        }

        // پست‌های منتشرشدهٔ وبلاگ
        CmsPost::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at'])
            ->each(function (CmsPost $post) use (&$urls) {
                $urls[] = [
                    'loc'        => route('blog.show', $post->slug),
                    'changefreq' => 'monthly',
                    'lastmod'    => optional($post->updated_at)->toAtomString(),
                ];
            });

        // پروفایل‌های عمومی هنرمندان فعال دارای username
        ArtistProfile::where('is_active', true)
            ->whereNotNull('username')
            ->orderByDesc('updated_at')
            ->get(['username', 'updated_at'])
            ->each(function (ArtistProfile $profile) use (&$urls) {
                $urls[] = [
                    'loc'        => route('profile.show', $profile->username),
                    'changefreq' => 'weekly',
                    'lastmod'    => optional($profile->updated_at)->toAtomString(),
                ];
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
