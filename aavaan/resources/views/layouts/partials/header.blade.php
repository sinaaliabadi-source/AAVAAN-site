<header id="site-header" style="background:var(--color-primary);position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(0,0,0,.18);">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;padding-top:.9rem;padding-bottom:.9rem;">

        <a href="{{ route('home') }}" style="font-family:'YekanBakh',Tahoma,sans-serif;font-size:1.55rem;font-weight:800;color:var(--color-accent);text-decoration:none;letter-spacing:-.5px;">
            آوان
        </a>

        <nav id="main-nav" style="display:flex;gap:1.6rem;align-items:center;">
            <a href="{{ route('about') }}"        style="color:#ccc;font-size:.9rem;transition:color .2s;" onmouseover="this.style.color='var(--color-accent)'" onmouseout="this.style.color='#ccc'">درباره ما</a>
            <a href="{{ route('how-it-works') }}" style="color:#ccc;font-size:.9rem;transition:color .2s;" onmouseover="this.style.color='var(--color-accent)'" onmouseout="this.style.color='#ccc'">نحوه کار</a>
            <a href="{{ route('pricing') }}"      style="color:#ccc;font-size:.9rem;transition:color .2s;" onmouseover="this.style.color='var(--color-accent)'" onmouseout="this.style.color='#ccc'">تعرفه‌ها</a>
            <a href="{{ route('blog') }}"         style="color:#ccc;font-size:.9rem;transition:color .2s;" onmouseover="this.style.color='var(--color-accent)'" onmouseout="this.style.color='#ccc'">وبلاگ</a>
        </nav>

        <div style="display:flex;gap:.75rem;align-items:center;">
            @auth
                @if(auth()->user()->isArtist())
                    <a href="{{ route('artist.dashboard') }}" class="btn btn-accent btn-sm">پنل هنرمند</a>
                @elseif(auth()->user()->isProduction())
                    <a href="{{ route('production.dashboard') }}" class="btn btn-accent btn-sm">پنل تولید</a>
                @endif
                <form method="POST" action="{{ route('auth.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="border-color:#aaa;color:#ccc;">خروج</button>
                </form>
            @else
                <a href="{{ route('auth') }}" class="btn btn-accent btn-sm">ورود / ثبت‌نام</a>
            @endauth
        </div>

        <button id="nav-toggle" aria-label="منو" style="display:none;background:none;border:none;cursor:pointer;color:#ccc;font-size:1.4rem;">☰</button>
    </div>

    <div id="mobile-nav" style="display:none;background:var(--color-primary);border-top:1px solid #333;padding:1rem 1.5rem;">
        <a href="{{ route('about') }}"        style="display:block;color:#ccc;padding:.5rem 0;border-bottom:1px solid #333;">درباره ما</a>
        <a href="{{ route('how-it-works') }}" style="display:block;color:#ccc;padding:.5rem 0;border-bottom:1px solid #333;">نحوه کار</a>
        <a href="{{ route('pricing') }}"      style="display:block;color:#ccc;padding:.5rem 0;border-bottom:1px solid #333;">تعرفه‌ها</a>
        <a href="{{ route('blog') }}"         style="display:block;color:#ccc;padding:.5rem 0;border-bottom:1px solid #333;">وبلاگ</a>
        @auth
            @if(auth()->user()->isArtist())
                <a href="{{ route('artist.dashboard') }}" style="display:block;color:var(--color-accent);padding:.5rem 0;">پنل هنرمند</a>
            @elseif(auth()->user()->isProduction())
                <a href="{{ route('production.dashboard') }}" style="display:block;color:var(--color-accent);padding:.5rem 0;">پنل تولید</a>
            @endif
        @else
            <a href="{{ route('auth') }}" style="display:block;color:var(--color-accent);padding:.5rem 0;">ورود / ثبت‌نام</a>
        @endauth
    </div>
</header>

<style>
@media (max-width: 768px) {
    #main-nav { display: none !important; }
    #nav-toggle { display: block !important; }
}
</style>
<script>
document.getElementById('nav-toggle').addEventListener('click', function () {
    var mn = document.getElementById('mobile-nav');
    mn.style.display = mn.style.display === 'none' ? 'block' : 'none';
});
</script>
