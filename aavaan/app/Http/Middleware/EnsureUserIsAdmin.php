<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('auth')->with('error', 'برای دسترسی به پنل مدیریت ابتدا وارد شوید.');
        }

        if (!auth()->user()->isAdmin()) {
            abort(403, 'دسترسی غیرمجاز.');
        }

        return $next($request);
    }
}
