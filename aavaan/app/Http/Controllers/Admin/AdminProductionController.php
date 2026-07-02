<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminProductionController extends Controller {

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = User::where('role', 'production')->with('productionAccesses');
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->search) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name','like',$s)->orWhere('email','like',$s));
        }
        $teams = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.production.index', compact('teams'));
    }

    public function show(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $team = User::where('role', 'production')->findOrFail($id);
        $accesses = $team->productionAccesses()->with(['payment'])->orderByDesc('created_at')->get();
        $totalPaid = $team->payments()->where('status', 'paid')->sum('amount');
        return view('admin.production.show', compact('team', 'accesses', 'totalPaid'));
    }
}
