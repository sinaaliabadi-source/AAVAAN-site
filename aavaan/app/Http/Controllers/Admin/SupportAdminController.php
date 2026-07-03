<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportCannedResponse;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class SupportAdminController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $stats = [
            'open'         => SupportTicket::where('status', 'open')->count(),
            'in_progress'  => SupportTicket::where('status', 'in_progress')->count(),
            'waiting_user' => SupportTicket::where('status', 'waiting_user')->count(),
            'resolved_today' => SupportTicket::where('status', 'resolved')
                ->whereDate('resolved_at', today())->count(),
            'urgent'       => SupportTicket::where('priority', 'urgent')
                ->whereNotIn('status', ['resolved', 'closed'])->count(),
            'today'        => SupportTicket::whereDate('created_at', today())->count(),
        ];

        // میانگین زمان اولین پاسخ (ساعت).
        $avgFirstResponse = SupportTicket::whereNotNull('first_response_at')
            ->get()
            ->avg(fn ($t) => $t->created_at->diffInMinutes($t->first_response_at) / 60);
        $stats['avg_first_response_hours'] = $avgFirstResponse ? round($avgFirstResponse, 1) : null;

        // نمودار هفتگی: تعداد تیکت‌های ۷ روز اخیر.
        $weekly = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = today()->subDays($i);
            $weekly[] = [
                'date'  => $day->format('Y-m-d'),
                'label' => $day->format('m/d'),
                'count' => SupportTicket::whereDate('created_at', $day)->count(),
            ];
        }

        $urgentTickets = SupportTicket::with('user')
            ->where('priority', 'urgent')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTickets = SupportTicket::with(['user', 'assignedAdmin'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.support.index', compact('stats', 'weekly', 'urgentTickets', 'recentTickets'));
    }

    public function tickets(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $query = SupportTicket::with(['user', 'assignedAdmin', 'latestMessage']);

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('department'))  $query->where('department', $request->department);
        if ($request->filled('priority'))    $query->where('priority', $request->priority);
        if ($request->filled('assigned_to')) $query->where('assigned_to', (int) $request->assigned_to);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ticket_number', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        match ($request->get('sort')) {
            'oldest'      => $query->oldest(),
            'last_reply'  => $query->latest('updated_at'),
            default       => $query->latest(),
        };

        $tickets = $query->paginate(20)->withQueryString();
        $admins  = User::where('role', 'admin')->orderBy('name')->get(['id', 'name']);

        return view('admin.support.tickets', compact('tickets', 'admins'));
    }

    public function show(SupportTicket $ticket)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $ticket->load(['user', 'assignedAdmin']);
        $messages = $ticket->messages()->with(['user', 'attachments'])->orderBy('created_at')->get();
        $admins   = User::where('role', 'admin')->orderBy('name')->get(['id', 'name']);
        $canned   = SupportCannedResponse::orderByDesc('use_count')->get();

        return view('admin.support.show', compact('ticket', 'messages', 'admins', 'canned'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'message'       => 'required|string|min:2|max:5000',
            'is_internal'   => 'nullable|boolean',
            'canned_id'     => 'nullable|integer|exists:support_canned_responses,id',
            'next_status'   => 'nullable|in:open,in_progress,waiting_user,resolved,closed',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,zip',
        ]);

        $isInternal = $request->boolean('is_internal');

        $message = $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'sender_type' => 'admin',
            'message'     => $request->message,
            'is_internal' => $isInternal,
        ]);

        $this->storeAttachments($request, $ticket, $message);

        // شمارش استفاده از پاسخ آماده.
        if ($request->filled('canned_id')) {
            SupportCannedResponse::where('id', $request->canned_id)->increment('use_count');
        }

        // فقط پاسخ عمومی روی وضعیت و زمان اولین پاسخ اثر می‌گذارد.
        if (! $isInternal) {
            $updates = [];
            if (is_null($ticket->first_response_at)) {
                $updates['first_response_at'] = now();
            }
            // وضعیت بعد از ارسال: انتخاب ادمین یا پیش‌فرض «در انتظار کاربر».
            $nextStatus = $request->filled('next_status') ? $request->next_status : 'waiting_user';
            $updates['status'] = $nextStatus;
            $this->applyStatusSideEffects($ticket, $nextStatus, $updates);

            $ticket->update($updates);

            $this->tryMailUser(
                $ticket,
                "پاسخ جدید برای تیکت {$ticket->ticket_number}",
                "کارشناسان آوان به تیکت شما ({$ticket->ticket_number}) پاسخ دادند. برای مشاهده وارد حساب خود شوید یا از بخش پیگیری تیکت استفاده کنید."
            );
        } else {
            $ticket->touch();
        }

        return back()->with('success', $isInternal ? 'یادداشت داخلی ثبت شد.' : 'پاسخ ارسال شد.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(['status' => 'required|in:open,in_progress,waiting_user,resolved,closed']);

        $old = $ticket->status;
        if ($old === $request->status) {
            return back();
        }

        $updates = ['status' => $request->status];
        $this->applyStatusSideEffects($ticket, $request->status, $updates);
        $ticket->update($updates);

        $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'sender_type' => 'system',
            'message'     => "وضعیت تیکت از «{$this->statusLabel($old)}» به «{$this->statusLabel($request->status)}» تغییر کرد.",
            'is_internal' => false,
        ]);

        $this->logAdminActivity('support_ticket_status', "وضعیت تیکت {$ticket->ticket_number} به {$request->status} تغییر کرد.", 'support_ticket', $ticket->id);

        return back()->with('success', 'وضعیت تیکت به‌روزرسانی شد.');
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(['assigned_to' => 'nullable|integer|exists:users,id']);

        $ticket->update(['assigned_to' => $request->assigned_to ?: null]);

        $name = $ticket->assigned_to ? (User::find($ticket->assigned_to)?->name ?? '—') : 'هیچ‌کس';
        $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'sender_type' => 'system',
            'message'     => "تیکت به {$name} واگذار شد.",
            'is_internal' => true,
        ]);

        return back()->with('success', 'انتساب تیکت به‌روزرسانی شد.');
    }

    public function updatePriority(Request $request, SupportTicket $ticket)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(['priority' => 'required|in:low,normal,high,urgent']);

        $ticket->update(['priority' => $request->priority]);

        return back()->with('success', 'اولویت تیکت به‌روزرسانی شد.');
    }

    // ─────────────────────────── canned responses ───────────────────────────

    public function cannedIndex()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $canned = SupportCannedResponse::with('creator')->latest()->paginate(30);
        return view('admin.support.canned', compact('canned'));
    }

    public function cannedStore(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'department' => 'nullable|in:technical,billing,casting,honarbaz,general',
            'content'    => 'required|string|max:5000',
        ]);
        $data['created_by'] = auth()->id();
        SupportCannedResponse::create($data);

        return back()->with('success', 'پاسخ آماده ذخیره شد.');
    }

    public function cannedUpdate(Request $request, int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $canned = SupportCannedResponse::findOrFail($id);
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'department' => 'nullable|in:technical,billing,casting,honarbaz,general',
            'content'    => 'required|string|max:5000',
        ]);
        $canned->update($data);

        return back()->with('success', 'پاسخ آماده به‌روزرسانی شد.');
    }

    public function cannedDestroy(int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        SupportCannedResponse::findOrFail($id)->delete();

        return back()->with('success', 'پاسخ آماده حذف شد.');
    }

    // ─────────────────────────── helpers ───────────────────────────

    private function applyStatusSideEffects(SupportTicket $ticket, string $status, array &$updates): void
    {
        if ($status === 'resolved' && is_null($ticket->resolved_at)) {
            $updates['resolved_at'] = now();
        }
        if ($status === 'closed' && is_null($ticket->closed_at)) {
            $updates['closed_at'] = now();
        }
    }

    private function storeAttachments(Request $request, SupportTicket $ticket, $message): void
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

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'open'         => 'باز',
            'in_progress'  => 'در حال بررسی',
            'waiting_user' => 'در انتظار کاربر',
            'resolved'     => 'حل شده',
            'closed'       => 'بسته شده',
            default        => $status,
        };
    }

    private function tryMailUser(SupportTicket $ticket, string $subject, string $body): void
    {
        $to = $ticket->requester_email;
        if (! $to) {
            return;
        }
        try {
            \Illuminate\Support\Facades\Mail::raw($body, fn ($m) => $m->to($to)->subject($subject));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
