<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDiscountController extends Controller {
    use LogsAdminActivity;

    public function index() {
        abort_unless(auth()->user()->role === 'admin', 403);
        $codes = DiscountCode::with('creator')->orderByDesc('created_at')->paginate(30);
        return view('admin.discounts.index', compact('codes'));
    }

    public function create() {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.discounts.create');
    }

    public function store(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:discount_codes,code',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'max_uses'    => 'nullable|integer|min:1',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active'   => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();
        $code = DiscountCode::create($validated);
        $this->logAdminActivity('discount_code_created', "کد تخفیف {$code->code} ایجاد شد.", 'discount_code', $code->id);
        return redirect()->route('admin.discounts.index')->with('success', 'کد تخفیف ایجاد شد.');
    }

    public function edit(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $code = DiscountCode::findOrFail($id);
        return view('admin.discounts.edit', compact('code'));
    }

    public function update(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $code = DiscountCode::findOrFail($id);
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:discount_codes,code,' . $id,
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'max_uses'    => 'nullable|integer|min:1',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active'   => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $code->update($validated);
        $this->logAdminActivity('discount_code_updated', "کد تخفیف {$code->code} ویرایش شد.", 'discount_code', $code->id);
        return back()->with('success', 'کد تخفیف به‌روزرسانی شد.');
    }

    public function toggle(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $code = DiscountCode::findOrFail($id);
        $code->update(['is_active' => !$code->is_active]);
        $status = $code->is_active ? 'فعال' : 'غیرفعال';
        $this->logAdminActivity('discount_code_toggled', "کد تخفیف {$code->code} {$status} شد.", 'discount_code', $code->id);
        return back()->with('success', "کد تخفیف {$status} شد.");
    }

    public function generateCode() {
        return response()->json(['code' => strtoupper(Str::random(8))]);
    }
}
