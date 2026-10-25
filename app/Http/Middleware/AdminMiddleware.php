<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('admin');

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if ($user->role !== 'admin') {
            abort(403, 'Kamu tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}
