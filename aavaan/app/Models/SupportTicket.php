<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number', 'user_id', 'guest_name', 'guest_email',
        'subject', 'department', 'priority', 'status',
        'assigned_to', 'first_response_at', 'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at'       => 'datetime',
        'closed_at'         => 'datetime',
    ];

    /** مسیرها با شمارهٔ تیکت (به‌جای id) کار می‌کنند. */
    public function getRouteKeyName(): string
    {
        return 'ticket_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SupportTicketMessage::class, 'ticket_id')->latestOfMany();
    }

    /**
     * تولید شمارهٔ یکتای تیکت — مثل TKT-2607-0001 (سال/ماه + شمارندهٔ ماهانه).
     */
    public static function generateTicketNumber(): string
    {
        $year  = now()->format('y');
        $month = now()->format('m');
        $count = static::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        return sprintf('TKT-%s%s-%04d', $year, $month, $count);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'         => 'باز',
            'in_progress'  => 'در حال بررسی',
            'waiting_user' => 'در انتظار کاربر',
            'resolved'     => 'حل شده',
            'closed'       => 'بسته شده',
            default        => $this->status,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low'    => 'کم',
            'normal' => 'معمولی',
            'high'   => 'زیاد',
            'urgent' => 'فوری',
            default  => $this->priority,
        };
    }

    public function getDepartmentLabelAttribute(): string
    {
        return match ($this->department) {
            'technical' => 'فنی',
            'billing'   => 'مالی و اشتراک',
            'casting'   => 'کستینگ',
            'honarbaz'  => 'هنرباز',
            'general'   => 'عمومی',
            default     => $this->department,
        };
    }

    /** نام نمایشیِ ثبت‌کنندهٔ تیکت (کاربر لاگین یا مهمان). */
    public function getRequesterNameAttribute(): string
    {
        return $this->user?->name ?? ($this->guest_name ?: 'مهمان');
    }

    public function getRequesterEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }
}
