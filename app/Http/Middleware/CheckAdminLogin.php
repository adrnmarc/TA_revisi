<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        // PENGUBAHAN: Cek menggunakan key 'admin_role' yang spesifik
        if (session('admin_role') !== 'admin') {
            return redirect('/login')->with('gagal', 'Anda tidak memiliki akses admin!');
        }

        return $next($request);
    }
}