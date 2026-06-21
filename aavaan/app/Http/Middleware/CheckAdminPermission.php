<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'دسترسی غیرمجاز.');
        }

        $adminRole = $user->admin_role;

        if (!$adminRole) {
            abort(403, 'نقش مدیریتی تعریف نشده است.');
        }

        $permissions = config('admin_permissions.' . $adminRole, []);

        if (in_array('*', $permissions) || in_array($section, $permissions)) {
            return $next($request);
        }

        abort(403, 'شما به این بخش دسترسی ندارید.');
    }
}
