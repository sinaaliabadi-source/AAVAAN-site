<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /** سوالات متداول ثابتِ صفحهٔ پشتیبانی. */
    private const FAQS = [
        ['q' => 'چطور اشتراک هنرمندی تهیه کنم؟', 'a' => 'پس از ثبت‌نام به‌عنوان هنرمند، از پنل کاربری بخش «اشتراک» را باز کنید و پلن ماهانه یا سالانه را انتخاب و پرداخت کنید.'],
        ['q' => 'اطلاعات تماس هنرمند چه زمانی نمایش داده می‌شود؟', 'a' => 'در کستینگ ناشناس، اطلاعات تماس و هویتیِ هنرمند تنها پس از خرید دسترسی توسط تیم تولید نمایش داده می‌شود.'],
        ['q' => 'چطور تیکت پشتیبانی خود را پیگیری کنم؟', 'a' => 'اگر کاربر هستید از بخش «تیکت‌های من» و اگر مهمان هستید از بخش «پیگیری تیکت» با وارد کردن شماره تیکت و ایمیل، وضعیت را ببینید.'],
        ['q' => 'در چه مدتی به تیکت‌ها پاسخ داده می‌شود؟', 'a' => 'میانگین زمان اولین پاسخ در روزهای کاری کمتر از چند ساعت است. تیکت‌های فوری در اولویت رسیدگی قرار می‌گیرند.'],
        ['q' => 'برنامهٔ هنرباز چیست؟', 'a' => 'هنرباز برنامهٔ استعدادیابی است که ثبت‌نام و رأی‌گیری عمومی دارد. برای پرسش‌های مربوط به آن، دپارتمان «هنرباز» را انتخاب کنید.'],
    ];

    private const DEPARTMENTS = [
        'technical' => ['label' => 'فنی', 'icon' => '🛠', 'desc' => 'مشکلات ورود، آپلود، نمایش پروفایل و باگ‌های سایت'],
        'billing'   => ['label' => 'مالی و اشتراک', 'icon' => '💳', 'desc' => 'پرداخت، اشتراک، فاکتور و بازگشت وجه'],
        'casting'   => ['label' => 'کستینگ', 'icon' => '🎬', 'desc' => 'دسترسی تیم تولید، جستجو و انتخاب هنرمند'],
        'honarbaz'  => ['label' => 'هنرباز', 'icon' => '🎭', 'desc' => 'ثبت‌نام و رأی‌گیری برنامهٔ استعدادیابی'],
        'general'   => ['label' => 'عمومی', 'icon' => '💬', 'desc' => 'سایر پرسش‌ها و پیشنهادها'],
    ];

    public function index()
    {
        // میانگین زمان اولین پاسخ (ساعت) برای نمایش «در چند ساعت پاسخ داده می‌شود».
        $avgHours = SupportTicket::whereNotNull('first_response_at')
            ->get()
            ->avg(fn ($t) => $t->created_at->diffInMinutes($t->first_response_at) / 60);

        return view('support.index', [
            'faqs'        => self::FAQS,
            'departments' => self::DEPARTMENTS,
            'avgHours'    => $avgHours ? max(1, (int) round($avgHours)) : null,
        ]);
    }

    public function create()
    {
        return view('support.create', ['departments' => self::DEPARTMENTS]);
    }

    public function store(Request $request)
    {
        $isGuest = ! auth()->check();

        $request->validate([
            'subject'        => 'required|string|max:255',
            'department'     => 'required|in:technical,billing,casting,honarbaz,general',
            'message'        => 'required|string|min:20|max:5000',
            'attachments.*'  => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,zip',
            'guest_name'     => ($isGuest ? 'required' : 'nullable') . '|string|max:100',
            'guest_email'    => ($isGuest ? 'required' : 'nullable') . '|email',
        ], [], [
            'subject'     => 'موضوع',
            'department'  => 'دپارتمان',
            'message'     => 'پیام',
            'guest_name'  => 'نام',
            'guest_email' => 'ایمیل',
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id'       => auth()->id(),
            'guest_name'    => $isGuest ? $request->guest_name : null,
            'guest_email'   => $isGuest ? $request->guest_email : null,
            'subject'       => $request->subject,
            'department'    => $request->department,
            'priority'      => 'normal',
            'status'        => 'open',
        ]);

        $message = $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'sender_type' => 'user',
            'message'     => $request->message,
            'is_internal' => false,
        ]);

        $this->storeAttachments($request, $ticket, $message);

        // دسترسی مهمان به این تیکت را در نشست ثبت می‌کنیم.
        $this->rememberAccess($ticket->ticket_number);

        $this->tryMail(
            $ticket->requester_email,
            "تیکت شما ثبت شد ({$ticket->ticket_number})",
            "تیکت پشتیبانی شما با شمارهٔ {$ticket->ticket_number} ثبت شد. کارشناسان ما به‌زودی پاسخ می‌دهند."
        );

        return redirect()
            ->route('support.show', $ticket->ticket_number)
            ->with('success', "تیکت شما با شمارهٔ {$ticket->ticket_number} ثبت شد.");
    }

    public function myTickets()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->withCount('messages')
            ->latest('updated_at')
            ->paginate(15);

        return view('support.my-tickets', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        abort_unless($this->canAccess($ticket), 403);

        // کاربر پیام‌های داخلی را نمی‌بیند.
        $messages = $ticket->messages()
            ->with(['user', 'attachments'])
            ->where('is_internal', false)
            ->orderBy('created_at')
            ->get();

        return view('support.show', compact('ticket', 'messages'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_unless($this->canAccess($ticket), 403);

        if (in_array($ticket->status, ['closed'], true)) {
            return back()->with('error', 'این تیکت بسته شده است و امکان ارسال پاسخ وجود ندارد.');
        }

        $request->validate([
            'message'       => 'required|string|min:2|max:5000',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,zip',
        ]);

        $message = $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'sender_type' => 'user',
            'message'     => $request->message,
            'is_internal' => false,
        ]);

        $this->storeAttachments($request, $ticket, $message);

        // پاسخ کاربر → تیکت دوباره در جریان بررسی قرار می‌گیرد.
        if (in_array($ticket->status, ['waiting_user', 'resolved'], true)) {
            $ticket->update(['status' => 'in_progress']);
        }
        $ticket->touch();

        return back()->with('success', 'پاسخ شما ثبت شد.');
    }

    public function close(SupportTicket $ticket)
    {
        abort_unless($this->canAccess($ticket), 403);

        if (! in_array($ticket->status, ['closed'], true)) {
            $ticket->update(['status' => 'closed', 'closed_at' => now()]);
            $ticket->messages()->create([
                'sender_type' => 'system',
                'message'     => 'تیکت توسط کاربر بسته شد.',
                'is_internal' => false,
            ]);
        }

        return back()->with('success', 'تیکت بسته شد.');
    }

    public function track()
    {
        return view('support.track');
    }

    public function trackResult(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
            'email'         => 'required|email',
        ]);

        $ticket = SupportTicket::where('ticket_number', $request->ticket_number)
            ->where(function ($q) use ($request) {
                $q->where('guest_email', $request->email)
                  ->orWhereHas('user', fn ($u) => $u->where('email', $request->email));
            })
            ->first();

        if (! $ticket) {
            return back()
                ->withInput()
                ->with('error', 'تیکتی با این شماره و ایمیل یافت نشد.');
        }

        $this->rememberAccess($ticket->ticket_number);

        return redirect()->route('support.show', $ticket->ticket_number);
    }

    public function downloadAttachment(SupportTicketAttachment $attachment)
    {
        $ticket = $attachment->message->ticket;
        abort_unless($this->canAccess($ticket) || $this->isAdmin(), 403);

        $full = storage_path('app/' . $attachment->file_path);
        abort_unless(is_file($full), 404);

        return response()->download($full, $attachment->original_name);
    }

    // ─────────────────────────── helpers ───────────────────────────

    /** ذخیرهٔ فایل‌های پیوست در storage/app/support/{ticket_id}/. */
    private function storeAttachments(Request $request, SupportTicket $ticket, SupportTicketMessage $message): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (! $file->isValid()) {
                continue;
            }
            $size = $file->getSize();
            $mime = $file->getClientMimeType();
            $original = $file->getClientOriginalName();

            $dir = storage_path("app/support/{$ticket->id}");
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = \Illuminate\Support\Str::uuid() . '.' . ($file->getClientOriginalExtension() ?: 'bin');
            $file->move($dir, $filename);

            SupportTicketAttachment::create([
                'message_id'    => $message->id,
                'file_path'     => "support/{$ticket->id}/{$filename}",
                'original_name' => $original,
                'file_size'     => $size,
                'mime_type'     => $mime,
                'created_at'    => now(),
            ]);
        }
    }

    private function canAccess(SupportTicket $ticket): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        if (auth()->check() && $ticket->user_id === auth()->id()) {
            return true;
        }

        return in_array($ticket->ticket_number, session('support_access', []), true);
    }

    private function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    private function rememberAccess(string $ticketNumber): void
    {
        $access = session('support_access', []);
        if (! in_array($ticketNumber, $access, true)) {
            $access[] = $ticketNumber;
            session(['support_access' => $access]);
        }
    }

    /** ارسال ایمیل به‌صورت امن — در نبود پیکربندی mail، خطا نادیده گرفته می‌شود. */
    private function tryMail(?string $to, string $subject, string $body): void
    {
        if (! $to) {
            return;
        }
        try {
            Mail::raw($body, function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
