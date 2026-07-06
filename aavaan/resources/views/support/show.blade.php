@extends('layouts.app')
@section('title', 'تیکت ' . $ticket->ticket_number . ' — آوان')

@push('styles')
<style>
    .ticket-wrap { max-width:800px; margin:0 auto; padding:2rem 1.5rem; }
    .ticket-head { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); padding:1.25rem 1.5rem; margin-bottom:1.25rem; }
    .ticket-head h1 { font-size:1.25rem; color:var(--color-primary); margin-bottom:.5rem; }
    .ticket-tags { display:flex; flex-wrap:wrap; gap:.5rem; }
    .tag { font-size:.78rem; padding:.2rem .7rem; border-radius:99px; background:#f0ede8; color:var(--color-muted); }
    .st { color:#fff; font-weight:600; }
    .st-open { background:#c0392b; } .st-in_progress { background:#e08600; }
    .st-waiting_user { background:#2980b9; } .st-resolved { background:#27852f; } .st-closed { background:#6b7280; }
    .chat { display:flex; flex-direction:column; gap:.9rem; margin-bottom:1.5rem; }
    .msg { max-width:82%; padding:.85rem 1.1rem; border-radius:14px; font-size:.92rem; line-height:1.9; }
    .msg .meta { font-size:.72rem; color:var(--color-muted); margin-top:.5rem; }
    .msg.user { align-self:flex-end; background:#f5efe3; border:1px solid #eadfc8; border-bottom-right-radius:4px; }
    .msg.admin { align-self:flex-start; background:#eaf1fb; border:1px solid #cfe0f5; border-bottom-left-radius:4px; }
    .msg.system { align-self:center; background:none; color:var(--color-muted); font-style:italic; font-size:.82rem; text-align:center; max-width:100%; }
    .msg .body { white-space:pre-line; }
    .att-link { display:inline-flex; align-items:center; gap:.35rem; font-size:.8rem; background:#fff; border:1px solid #ddd6c8; border-radius:6px; padding:.2rem .6rem; margin-top:.5rem; margin-left:.35rem; text-decoration:none; color:var(--color-primary); }
    .reply-box { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); padding:1.25rem; }
</style>
@endpush

@section('content')
<div class="ticket-wrap">

    <a href="{{ auth()->check() ? route('support.tickets') : route('support.index') }}" style="font-size:.85rem;color:var(--color-muted)">→ بازگشت</a>

    <div class="ticket-head" style="margin-top:.75rem">
        <h1>{{ $ticket->subject }}</h1>
        <div class="ticket-tags">
            <span class="tag" style="direction:ltr">{{ $ticket->ticket_number }}</span>
            <span class="tag">{{ $ticket->department_label }}</span>
            <span class="tag">اولویت: {{ $ticket->priority_label }}</span>
            <span class="tag st st-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
        </div>
    </div>

    {{-- مکالمه --}}
    <div class="chat">
        @foreach($messages as $msg)
            @php $type = $msg->sender_type; @endphp
            <div class="msg {{ $type }}">
                @if($type !== 'system')
                <div class="body">{{ $msg->message }}</div>
                @foreach($msg->attachments as $att)
                    <a class="att-link" href="{{ route('support.attachment.download', $att->id) }}">📎 {{ $att->original_name }} <span style="color:var(--color-muted)">({{ $att->human_size }})</span></a>
                @endforeach
                <div class="meta">
                    {{ $type === 'admin' ? 'پشتیبانی آوان' : ($msg->user?->name ?? $ticket->requester_name) }}
                    — {{ $msg->created_at->diffForHumans() }}
                </div>
                @else
                <div class="body">{{ $msg->message }} · {{ $msg->created_at->diffForHumans() }}</div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- فرم پاسخ / بستن --}}
    @if($ticket->status === 'closed')
        <div class="alert" style="background:#f0ede8;color:var(--color-muted);text-align:center">این تیکت بسته شده است.</div>
    @else
        <div class="reply-box">
            <form method="POST" action="{{ route('support.reply', $ticket->ticket_number) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>پاسخ شما</label>
                    <textarea name="message" class="form-control" rows="4" maxlength="5000" required placeholder="پاسخ خود را بنویسید...">{{ old('message') }}</textarea>
                </div>
                <div class="form-group">
                    <label style="font-size:.85rem">پیوست (اختیاری)</label>
                    <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.zip">
                </div>
                <div style="display:flex;gap:.6rem;align-items:center">
                    <button type="submit" class="btn btn-primary btn-sm">ارسال پاسخ</button>
                </div>
            </form>

            @if($ticket->status === 'resolved')
            <form method="POST" action="{{ route('support.close', $ticket->ticket_number) }}" style="margin-top:1rem;border-top:1px solid #f0ede8;padding-top:1rem">
                @csrf
                <p style="font-size:.85rem;color:var(--color-muted);margin-bottom:.5rem">مشکل شما حل شده است؟</p>
                <button type="submit" class="btn btn-ghost btn-sm">✔ بستن تیکت</button>
            </form>
            @endif
        </div>
    @endif

</div>
@endsection
