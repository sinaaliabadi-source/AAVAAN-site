@extends('layouts.app')

@section('title', 'شرکت‌کنندگان هنرباز — آوان')
@section('meta-description', 'فهرست شرکت‌کنندگان تأییدشده برنامه استعدادیابی هنرباز و رأی‌گیری مردمی.')

@push('styles')
<style>
    .hb-c-head { text-align: center; margin: 2.5rem 0 1.5rem; }
    .hb-c-head h1 { font-size: 2.2rem; }
    .hb-c-head p { color: var(--color-muted); }

    .hb-filters { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; margin-bottom: 2rem; }
    .hb-filters select { min-width: 190px; }

    .hb-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.4rem; }
    .hb-card {
        background: #fff; border-radius: 16px; padding: 1.6rem 1.3rem; text-align: center;
        box-shadow: var(--shadow); border: 1px solid rgba(31,42,68,.06);
        display: flex; flex-direction: column; transition: transform .2s, box-shadow .2s;
    }
    .hb-card:hover { transform: translateY(-4px); box-shadow: 0 10px 26px rgba(31,42,68,.14); }
    .hb-card .avatar {
        width: 72px; height: 72px; border-radius: 50%; margin: 0 auto 1rem;
        background: linear-gradient(135deg,#1F2A44,#C9A24B); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700;
    }
    .hb-card h3 { font-size: 1.15rem; margin-bottom: .3rem; }
    .hb-card .meta { font-size: .85rem; color: var(--color-muted); margin-bottom: .2rem; }
    .hb-card .talent { display: inline-block; background: var(--color-bg); color: var(--color-primary); border-radius: 999px; padding: .2rem .8rem; font-size: .8rem; font-weight: 600; margin: .5rem 0; }
    .hb-card .votes { font-weight: 800; color: var(--color-accent); font-size: 1.4rem; }
    .hb-card .votes small { color: var(--color-muted); font-weight: 400; font-size: .8rem; }
    .hb-card .btn { margin-top: auto; }

    .hb-modal-backdrop {
        position: fixed; inset: 0; background: rgba(20,28,48,.6); z-index: 1000;
        display: flex; align-items: center; justify-content: center; padding: 1rem;
    }
    .hb-modal {
        background: #fff; border-radius: 16px; padding: 2rem; max-width: 420px; width: 100%;
        box-shadow: 0 20px 60px rgba(0,0,0,.3);
    }
    .hb-modal h3 { margin-bottom: .4rem; }
    .hb-modal .sub { color: var(--color-muted); font-size: .9rem; margin-bottom: 1.2rem; }
    .hb-modal-close { float: left; background: none; border: none; font-size: 1.4rem; cursor: pointer; color: var(--color-muted); }
    .hb-msg { padding: .6rem .8rem; border-radius: 8px; font-size: .88rem; margin-bottom: 1rem; }
    .hb-msg.ok { background: #ecf5ec; color: #1a4a1a; }
    .hb-msg.bad { background: #fdecea; color: #7f1d1d; }

    @media (max-width: 1000px) { .hb-grid { grid-template-columns: repeat(3,1fr); } }
    @media (max-width: 760px) { .hb-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 480px) { .hb-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container" x-data="honarbazVote()">
    <div class="hb-c-head">
        <h1>شرکت‌کنندگان هنرباز</h1>
        <p>به استعداد محبوب خود رأی دهید — از هر دستگاه یک رأی</p>
    </div>

    {{-- فیلترها --}}
    <form method="GET" class="hb-filters">
        <select name="province" class="form-control" onchange="this.form.submit()">
            <option value="">همه استان‌ها</option>
            @foreach($provinces as $p)
                <option value="{{ $p }}" @selected(request('province')===$p)>{{ $p }}</option>
            @endforeach
        </select>
        <select name="talent_type" class="form-control" onchange="this.form.submit()">
            <option value="">همه رشته‌ها</option>
            @foreach($talentTypes as $t)
                <option value="{{ $t }}" @selected(request('talent_type')===$t)>{{ $t }}</option>
            @endforeach
        </select>
        @if(request('province') || request('talent_type'))
            <a href="{{ route('honarbaz.contestants') }}" class="btn btn-outline">حذف فیلترها</a>
        @endif
    </form>

    @if($contestants->isEmpty())
        <div class="card" style="text-align:center">هنوز شرکت‌کننده تأییدشده‌ای وجود ندارد.</div>
    @else
        <div class="hb-grid">
            @foreach($contestants as $c)
                <div class="hb-card">
                    <div class="avatar">{{ mb_substr($c->firstName(), 0, 1) }}</div>
                    <h3>{{ $c->firstName() }}</h3>
                    <div class="meta">📍 {{ $c->province }}</div>
                    <span class="talent">{{ $c->talent_type }}</span>
                    <div class="votes" x-text="voteCounts[{{ $c->id }}] ?? {{ $c->votes_count }}"></div>
                    <div style="margin-bottom:.8rem"><small style="color:var(--color-muted)">رأی</small></div>
                    @if($votingOpen)
                        <button class="btn btn-accent btn-block" @click="openVote({{ $c->id }}, @js($c->firstName()))">رأی بده</button>
                    @else
                        <span class="btn btn-outline btn-block" style="cursor:default">رأی‌گیری بسته است</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div style="margin-top:2rem">
            {{ $contestants->links() }}
        </div>
    @endif

    {{-- ===== Modal رأی‌گیری ===== --}}
    <template x-if="modalOpen">
        <div class="hb-modal-backdrop" @click.self="closeModal()">
            <div class="hb-modal">
                <button class="hb-modal-close" @click="closeModal()">✕</button>
                <h3>رأی به <span x-text="targetName"></span></h3>
                <p class="sub">برای ثبت رأی، شماره موبایل خود را وارد کنید.</p>

                <div class="hb-msg" :class="msgType" x-show="msg" x-text="msg"></div>

                {{-- مرحله ۱: شماره موبایل --}}
                <div x-show="phase === 'phone'">
                    <div class="form-group">
                        <label>شماره موبایل</label>
                        <input type="tel" class="form-control" x-model="phone" placeholder="۰۹...">
                    </div>
                    <button class="btn btn-primary btn-block" @click="sendCode()" :disabled="loading" x-text="loading ? 'در حال ارسال...' : 'دریافت کد تأیید'"></button>
                </div>

                {{-- مرحله ۲: کد تأیید --}}
                <div x-show="phase === 'code'">
                    <div class="form-group">
                        <label>کد تأیید ۶ رقمی</label>
                        <input type="text" inputmode="numeric" maxlength="6" class="form-control" x-model="code" placeholder="------">
                        <small class="hb-hint" x-show="debugCode">کد آزمایشی: <b x-text="debugCode"></b></small>
                    </div>
                    <button class="btn btn-accent btn-block" @click="verify()" :disabled="loading" x-text="loading ? 'در حال بررسی...' : 'ثبت رأی'"></button>
                </div>

                {{-- موفقیت --}}
                <div x-show="phase === 'done'" style="text-align:center">
                    <div style="font-size:3rem">✅</div>
                    <p>رأی شما ثبت شد. سپاسگزاریم!</p>
                    <button class="btn btn-outline btn-block" @click="closeModal()">بستن</button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function honarbazVote() {
    return {
        modalOpen: false,
        phase: 'phone',       // phone | code | done
        targetId: null,
        targetName: '',
        phone: '',
        code: '',
        voteId: null,
        debugCode: null,
        loading: false,
        msg: '',
        msgType: 'bad',
        voteCounts: {},
        csrf: document.querySelector('meta[name="csrf-token"]').content,

        openVote(id, name) {
            this.targetId = id;
            this.targetName = name;
            this.phase = 'phone';
            this.phone = ''; this.code = ''; this.voteId = null; this.debugCode = null;
            this.msg = '';
            this.modalOpen = true;
        },
        closeModal() { this.modalOpen = false; },
        setMsg(text, type) { this.msg = text; this.msgType = type; },

        async post(url, body) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                body: JSON.stringify(body),
            });
            const data = await res.json().catch(() => ({ ok: false, message: 'خطای غیرمنتظره.' }));
            return { status: res.status, data };
        },

        async sendCode() {
            this.msg = '';
            if (!/^09[0-9]{9}$/.test(this.toEn(this.phone))) { this.setMsg('شماره موبایل معتبر نیست.', 'bad'); return; }
            this.loading = true;
            const { data } = await this.post(@js(route('honarbaz.vote')), {
                registration_id: this.targetId,
                phone: this.toEn(this.phone),
            });
            this.loading = false;
            if (data.ok) {
                this.voteId = data.vote_id;
                this.debugCode = data.debug_code || null;
                this.phase = 'code';
                this.setMsg(data.message, 'ok');
            } else {
                this.setMsg(data.message, 'bad');
            }
        },

        async verify() {
            this.msg = '';
            if (!/^[0-9]{6}$/.test(this.toEn(this.code))) { this.setMsg('کد باید ۶ رقم باشد.', 'bad'); return; }
            this.loading = true;
            const { data } = await this.post(@js(route('honarbaz.vote.verify')), {
                vote_id: this.voteId,
                code: this.toEn(this.code),
            });
            this.loading = false;
            if (data.ok) {
                this.voteCounts[this.targetId] = data.votes_count;
                this.phase = 'done';
                this.setMsg('', 'ok');
            } else {
                this.setMsg(data.message, 'bad');
            }
        },

        toEn(s) {
            return (s + '').replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
                             .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
        },
    };
}
</script>
@endpush
