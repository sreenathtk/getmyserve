<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            if ($request->user()) {
                return match($request->user()->role) {
                    'admin' => redirect('/admin/dashboard'),
                    'service_provider' => redirect('/provider/dashboard'),
                    'staff' => redirect('/staff/dashboard'),
                    default => redirect('/'),
                };
            }
            return redirect('/login');
        }

        return $next($request);
    }
}
