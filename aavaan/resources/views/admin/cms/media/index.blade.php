@extends('admin.layouts.app')
@section('title', 'کتابخانهٔ رسانه')
@section('page-title', 'کتابخانهٔ رسانه')

@push('styles')
<style>
.dropzone { border:2px dashed #d5cfc4; border-radius:var(--radius); padding:2rem; text-align:center; color:var(--color-muted); background:#faf7f2; cursor:pointer; transition:border-color .15s, background .15s; }
.dropzone.drag { border-color:var(--color-accent); background:#f5efe0; }
.media-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:1rem; margin-top:1.25rem; }
.media-item { background:#fff; border:1px solid #ece6da; border-radius:10px; overflow:hidden; position:relative; }
.media-thumb { height:120px; background:#f0ece0 center/cover no-repeat; display:flex; align-items:center; justify-content:center; font-size:1.8rem; color:var(--color-muted); cursor:pointer; }
.media-info { padding:.5rem .6rem; font-size:.75rem; }
.media-info .mn { color:var(--color-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.media-info .ms { color:var(--color-muted); }
.media-del { position:absolute; top:.35rem; left:.35rem; background:rgba(192,57,43,.9); color:#fff; border:none; border-radius:6px; width:26px; height:26px; cursor:pointer; }
.copy-toast { position:fixed; bottom:1.5rem; left:50%; transform:translateX(-50%); background:#27852f; color:#fff; padding:.6rem 1.2rem; border-radius:8px; font-size:.85rem; z-index:999; }
</style>
@endpush

@section('content')
<div x-data="mediaLib()">
    {{-- Upload --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="dropzone" :class="{ 'drag': dragging }"
             @click="$refs.file.click()"
             @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
             @drop.prevent="dragging=false; handleFiles($event.dataTransfer.files)">
            <div style="font-size:1.8rem">⬆️</div>
            <p>فایل‌ها را اینجا رها کنید یا کلیک کنید</p>
            <p style="font-size:.78rem">jpg, png, gif, webp, pdf — حداکثر ۱۰ مگابایت</p>
            <input type="file" x-ref="file" style="display:none" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
                   @change="handleFiles($event.target.files)">
        </div>
        <p x-show="uploading" style="margin-top:.75rem;color:var(--color-muted);font-size:.85rem">در حال آپلود…</p>
    </div>

    {{-- Filter --}}
    <div style="display:flex;gap:.5rem;margin-bottom:1rem">
        <a href="{{ route('admin.cms.media.index') }}" class="btn btn-ghost btn-sm {{ !request('type') ? 'btn-primary' : '' }}">همه</a>
        <a href="{{ route('admin.cms.media.index', ['type'=>'image']) }}" class="btn btn-ghost btn-sm {{ request('type')==='image' ? 'btn-primary' : '' }}">تصاویر</a>
        <a href="{{ route('admin.cms.media.index', ['type'=>'file']) }}" class="btn btn-ghost btn-sm {{ request('type')==='file' ? 'btn-primary' : '' }}">فایل‌ها</a>
    </div>

    {{-- Grid --}}
    <div class="card">
        @if($media->count())
        <div class="media-grid">
            @foreach($media as $m)
            <div class="media-item">
                <button class="media-del" onclick="event.stopPropagation(); if(confirm('حذف فایل؟')) { document.getElementById('del-{{ $m->id }}').submit(); }">✕</button>
                <form id="del-{{ $m->id }}" method="POST" action="{{ route('admin.cms.media.destroy', $m->id) }}">@csrf @method('DELETE')</form>
                @if($m->is_image)
                    <div class="media-thumb" style="background-image:url('{{ $m->url }}')" title="کپی URL" @click="copy('{{ $m->url }}')"></div>
                @else
                    <div class="media-thumb" title="کپی URL" @click="copy('{{ $m->url }}')">📄</div>
                @endif
                <div class="media-info">
                    <div class="mn" title="{{ $m->original_name }}">{{ $m->original_name }}</div>
                    <div class="ms">{{ $m->human_size }}@if($m->width) · {{ $m->width }}×{{ $m->height }}@endif</div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:1.25rem">{{ $media->links() }}</div>
        @else
        <p style="text-align:center;padding:2rem;color:var(--color-muted)">فایلی آپلود نشده است.</p>
        @endif
    </div>

    <div class="copy-toast" x-show="toast" x-cloak x-transition style="display:none">✓ آدرس کپی شد</div>
</div>
@endsection

@push('scripts')
<script>
    function mediaLib() {
        return {
            dragging: false, uploading: false, toast: false,
            handleFiles(files) {
                if (!files.length) return;
                this.uploading = true;
                const uploads = Array.from(files).map(file => {
                    const fd = new FormData();
                    fd.append('file', file);
                    fd.append('_token', '{{ csrf_token() }}');
                    return fetch('{{ route('admin.cms.media.upload') }}', { method:'POST', body:fd, headers:{'Accept':'application/json'} });
                });
                Promise.all(uploads).then(() => { window.location.reload(); }).catch(() => { this.uploading=false; alert('آپلود ناموفق'); });
            },
            copy(url) {
                const abs = url.startsWith('http') ? url : (window.location.origin + '/' + url.replace(/^\//,''));
                navigator.clipboard.writeText(abs).then(() => {
                    this.toast = true; setTimeout(() => this.toast = false, 1800);
                });
            },
        };
    }
</script>
@endpush
