@props([
    'size'  => 18,
    'title' => 'تیک آبی آوان — پروفایل برگزیده و مورد اعتماد',
])

{{-- «تیک آبی آوان» — نشان برگزیدگیِ کلِ پروفایل که فقط ادمین اعطا می‌کند.
     جدا از «تأیید تخصص» است. آیکون تیک داخل دایره، رنگ آبیِ برند. --}}
<svg {{ $attributes->merge(['class' => 'aavaan-blue-tick']) }}
     xmlns="http://www.w3.org/2000/svg"
     width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     role="img" aria-label="{{ $title }}" style="vertical-align:middle;flex-shrink:0;">
    <title>{{ $title }}</title>
    <circle cx="12" cy="12" r="11" fill="#1F73C9"/>
    <path d="M7 12.4l3.1 3.1L17 8.6" fill="none" stroke="#fff" stroke-width="2.2"
          stroke-linecap="round" stroke-linejoin="round"/>
</svg>
