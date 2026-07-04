<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller {

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = Payment::with('user');
        if ($request->status) $query->where('status', $request->status);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->payable_type) {
            $type = $request->payable_type === 'subscription' ? 'App\\Models\\Subscription' : 'App\\Models\\ProductionAccess';
            $query->where('payable_type', $type);
        }
        $payments = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.payments.index', compact('payments'));
    }

    public function export(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = Payment::with('user');
        if ($request->status) $query->where('status', $request->status);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        $payments = $query->orderByDesc('created_at')->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename=payments.csv'];
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
            fputcsv($file, ['شناسه','کاربر','ایمیل','مبلغ','وضعیت','نوع','روش','تاریخ']);
            foreach ($payments as $p) {
                $type   = str_contains($p->payable_type, 'Subscription') ? 'اشتراک' : 'دسترسی تولید';
                $source = $p->payment_source === 'manual' ? 'دستی' : 'درگاه';
                fputcsv($file, [$p->id, $p->user?->name, $p->user?->email, $p->amount, $p->status, $type, $source, $p->created_at?->format('Y-m-d H:i')]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
