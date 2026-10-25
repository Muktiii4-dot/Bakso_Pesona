<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('customer');

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'customer') {
            abort(403, 'Akun ini tidak memiliki akses pelanggan.');
        }

        return $next($request);
    }
}
