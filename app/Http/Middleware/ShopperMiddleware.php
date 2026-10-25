<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShopperMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('customer');

        if ($user && $user->role !== 'customer') {
            abort(403, 'Keranjang hanya tersedia untuk pelanggan.');
        }

        if (! $user && Auth::guard('admin')->check()) {
            abort(403, 'Keranjang hanya tersedia untuk pelanggan.');
        }

        return $next($request);
    }
}
