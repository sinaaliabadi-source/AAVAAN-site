<?php

namespace App\Traits;

use App\Models\AdminActivityLog;

trait LogsAdminActivity
{
    protected function logAdminActivity(
        string $action,
        string $description = '',
        ?string $subjectType = null,
        ?int $subjectId = null
    ): void {
        AdminActivityLog::create([
            'admin_user_id' => auth()->id(),
            'action'        => $action,
            'subject_type'  => $subjectType,
            'subject_id'    => $subjectId,
            'description'   => $description,
            'ip_address'    => request()->ip(),
            'created_at'    => now(),
        ]);
    }
}
