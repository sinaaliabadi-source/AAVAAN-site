<footer style="background: var(--color-primary); color: #aaa; padding: 3rem 0; margin-top: 4rem;">
    <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
        <div>
            <a href="{{ route('home') }}" style="text-decoration:none;display:inline-flex;align-items:center;margin-bottom:.9rem;" aria-label="آوان — صفحه اصلی">
                @include('layouts.partials.logo', ['height' => '36px', 'variant' => 'light'])
            </a>
            <p style="font-size: 0.9rem;">پلتفرم تخصصی کاستینگ هنرمندان ایران</p>
        </div>
        <div>
            <h4 style="color: #fff; margin-bottom: 1rem;">دسترسی سریع</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                <li><a href="{{ route('about') }}" style="color: #aaa;">درباره ما</a></li>
                <li><a href="{{ route('how-it-works') }}" style="color: #aaa;">نحوه کار</a></li>
                <li><a href="{{ route('faq') }}" style="color: #aaa;">سوالات متداول</a></li>
            </ul>
        </div>
        <div>
            <h4 style="color: #fff; margin-bottom: 1rem;">قانونی</h4>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                <li><a href="{{ route('terms') }}" style="color: #aaa;">شرایط استفاده</a></li>
                <li><a href="{{ route('privacy') }}" style="color: #aaa;">حریم خصوصی</a></li>
            </ul>
        </div>
        <div>
            <h4 style="color: #fff; margin-bottom: 1rem;">تماس</h4>
            <p style="font-size: 0.9rem;">info@aavaan.com</p>
        </div>
    </div>
    <div class="container" style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #333; text-align: center; font-size: 0.85rem;">
        &copy; {{ date('Y') }} آوان - تمامی حقوق محفوظ است
    </div>
</footer>
