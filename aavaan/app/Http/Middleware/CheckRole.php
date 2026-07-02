<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('auth')->with('error', 'برای دسترسی به این بخش ابتدا وارد شوید.');
        }

        if (auth()->user()->role !== $role) {
            return redirect()->route('home')->with('error', 'دسترسی غیرمجاز.');
        }

        return $next($request);
    }
}
