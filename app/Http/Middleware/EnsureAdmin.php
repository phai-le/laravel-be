<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check() || ! (Auth::guard('admin')->user() instanceof Admin)) {
            return response()->json(['message' => 'Unauthorized Admin'], 403);
        }

        Auth::setUser(Auth::guard('admin')->user());

        return $next($request);
    }
}
