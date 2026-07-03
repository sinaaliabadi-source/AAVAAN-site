@extends('admin.layouts.app')
@section('title', 'تیکت ' . $ticket->ticket_number)
@section('page-title', 'مدیریت تیکت')
@section('topbar-actions')
<a href="{{ route('admin.support.tickets') }}" class="btn btn-ghost btn-sm">→ همه تیکت‌ها</a>
@endsection

@push('styles')
<style>
    .st { color:#fff; font-weight:600; font-size:.74rem; padding:.15rem .6rem; border-radius:99px; white-space:nowrap; }
    .st-open { background:#c0392b; } .st-in_progress { background:#e08600; }
    .st-waiting_user { background:#2980b9; } .st-resolved { background:#27852f; } .st-closed { background:#6b7280; }
    .ctrl-row { display:flex; flex-wrap:wrap; gap:.75rem; align-items:end; }
    .ctrl-row .form-group { margin:0; min-width:150px; }
    .chat { display:flex; flex-direction:column; gap:.85rem; }
    .msg { max-width:82%; padding:.8rem 1.05rem; border-radius:14px; font-size:.9rem; line-height:1.85; }
    .msg .meta { font-size:.72rem; color:var(--color-muted); margin-top:.45rem; }
    .msg.user { align-self:flex-end; background:#f5efe3; border:1px solid #eadfc8; }
    .msg.admin { align-self:flex-start; background:#eaf1fb; border:1px solid #cfe0f5; }
    .msg.system { align-self:center; background:none; color:var(--color-muted); font-style:italic; font-size:.8rem; text-align:center; max-width:100%; }
    .msg.internal { align-self:stretch; max-width:100%; background:#fff7db; border:1px dashed #e6cf7a; }
    .msg .body { white-space:pre-line; }
    .att-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.8rem; background:#fff; border:1px solid #ddd6c8; border-radius:6px; padding:.15rem .55rem; margin-top:.4rem; margin-left:.3rem; text-decoration:none; color:var(--color-primary); }
</style>
@endpush

@section('content')

{{-- بالا: اطلاعات + کنترل‌ها --}}
<div class="card" style="margin-bottom:1.25rem">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem">
        <div>
            <h2 style="font-size:1.15rem;color:var(--color-primary);margin-bottom:.35rem">{{ $ticket->subject }}</h2>
            <div style="font-size:.82rem;color:var(--color-muted)">
                <span style="direction:ltr;display:inline-block">{{ $ticket->ticket_number }}</span>
                · {{ $ticket->department_label }}
                · {{ $ticket->requester_name }}
                @if($ticket->requester_email) · <span dir="ltr">{{ $ticket->requester_email }}</span> @endif
                · {{ $ticket->created_at->format('Y/m/d H:i') }}
            </div>
        </div>
        <span class="st st-{{ $ticket->status }}" style="height:fit-content">{{ $ticket->status_label }}</span>
    </div>

    <div class="ctrl-row">
        <form method="POST" action="{{ route('admin.support.status', $ticket->ticket_number) }}" class="form-group">
            @csrf
            <label>وضعیت</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                @foreach(['open'=>'باز','in_progress'=>'در حال بررسی','waiting_user'=>'در انتظار کاربر','resolved'=>'حل شده','closed'=>'بسته شده'] as $k=>$v)
                    <option value="{{ $k }}" {{ $ticket->status===$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
        </form>

        <form method="POST" action="{{ route('admin.support.priority', $ticket->ticket_number) }}" class="form-group">
            @csrf
            <label>اولویت</label>
            <select name="priority" class="form-control" onchange="this.form.submit()">
                @foreach(['low'=>'کم','normal'=>'معمولی','high'=>'زیاد','urgent'=>'فوری'] as $k=>$v)
                    <option value="{{ $k }}" {{ $ticket->priority===$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
        </form>

        <form method="POST" action="{{ route('admin.support.assign', $ticket->ticket_number) }}" class="form-group">
            @csrf
            <label>مسئول رسیدگی</label>
            <select name="assigned_to" class="form-control" onchange="this.form.submit()">
                <option value="">— هیچ‌کس —</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ (string)$ticket->assigned_to===(string)$a->id?'selected':'' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- وسط: مکالمه --}}
<div class="card" style="margin-bottom:1.25rem">
    <div class="card-title">💬 مکالمه</div>
    <div class="chat">
        @foreach($messages as $msg)
            @php
                $cls = $msg->is_internal ? 'internal' : $msg->sender_type;
            @endphp
            <div class="msg {{ $cls }}">
                @if($msg->sender_type === 'system')
                    <div class="body">{{ $msg->message }} · {{ $msg->created_at->diffForHumans() }}</div>
                @else
                    @if($msg->is_internal)<div style="font-size:.72rem;color:#8a6d00;margin-bottom:.3rem">🔒 یادداشت داخلی</div>@endif
                    <div class="body">{{ $msg->message }}</div>
                    @foreach($msg->attachments as $att)
                        <a class="att-link" href="{{ route('support.attachment.download', $att->id) }}">📎 {{ $att->original_name }} <span style="color:var(--color-muted)">({{ $att->human_size }})</span></a>
                    @endforeach
                    <div class="meta">
                        {{ $msg->sender_type === 'admin' ? ('پشتیبانی' . ($msg->user ? ' — '.$msg->user->name : '')) : $ticket->requester_name }}
                        · {{ $msg->created_at->format('Y/m/d H:i') }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- پایین: فرم پاسخ --}}
<div class="card"
     x-data="{
        canned: @js($canned->map(fn($c)=>['id'=>$c->id,'title'=>$c->title,'content'=>$c->content])),
        body: '',
        applyCanned(id) {
            if(!id) return;
            const c = this.canned.find(x => x.id == id);
            if(c){ this.body = this.body ? (this.body + '\n' + c.content) : c.content; }
        }
     }">
    <div class="card-title">✍️ پاسخ</div>
    <form method="POST" action="{{ route('admin.support.reply', $ticket->ticket_number) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>پاسخ آماده</label>
            <select class="form-control" @change="applyCanned($event.target.value); $event.target.value=''">
                <option value="">— درج پاسخ آماده —</option>
                <template x-for="c in canned" :key="c.id">
                    <option :value="c.id" x-text="c.title"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>متن پاسخ</label>
            <textarea name="message" class="form-control" rows="5" maxlength="5000" required x-model="body" placeholder="پاسخ خود را بنویسید...">{{ old('message') }}</textarea>
        </div>

        <div class="ctrl-row" style="margin-bottom:1rem">
            <div class="form-group">
                <label>پیوست</label>
                <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.zip">
            </div>
            <div class="form-group">
                <label>وضعیت پس از ارسال</label>
                <select name="next_status" class="form-control">
                    <option value="waiting_user">در انتظار کاربر</option>
                    <option value="in_progress">در حال بررسی</option>
                    <option value="resolved">حل شده</option>
                    <option value="closed">بسته شده</option>
                </select>
            </div>
            <label style="display:flex;align-items:center;gap:.4rem;font-size:.88rem;margin-bottom:.4rem">
                <input type="checkbox" name="is_internal" value="1"> 🔒 یادداشت داخلی (کاربر نمی‌بیند)
            </label>
        </div>

        <button type="submit" class="btn btn-primary">ارسال</button>
    </form>
</div>

@endsection
