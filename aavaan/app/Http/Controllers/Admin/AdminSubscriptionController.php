<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller {
    use LogsAdminActivity;

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = Subscription::with('user');
        if ($request->status === 'active') $query->where('status', 'active')->where('expires_at', '>', now());
        elseif ($request->status === 'expired') $query->where(fn($q) => $q->where('status', 'expired')->orWhere('expires_at', '<=', now()));
        elseif ($request->status) $query->where('status', $request->status);
        if ($request->plan) $query->where('plan', $request->plan);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->search) {
            $s = '%' . $request->search . '%';
            $query->whereHas('user', fn($q) => $q->where('name','like',$s)->orWhere('email','like',$s));
        }
        $subscriptions = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $subscription = Subscription::with(['user', 'payment'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => 'expired']);
        $this->logAdminActivity('subscription_cancelled', "اشتراک #{$id} لغو شد.", 'subscription', $id);
        return back()->with('success', 'اشتراک لغو شد.');
    }

    public function extend(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(['expires_at' => 'required|date|after:today']);
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => 'active', 'expires_at' => $request->expires_at]);
        $this->logAdminActivity('subscription_extended', "اشتراک #{$id} تمدید شد تا {$request->expires_at}.", 'subscription', $id);
        return back()->with('success', 'اشتراک تمدید شد.');
    }
}
