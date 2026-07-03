<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya membatasi jika bukan admin
        if (session('role') !== 'admin') {
            return redirect('/login')->with('gagal', 'Anda tidak memiliki akses admin!');
        }

        return $next($request);
    }
}