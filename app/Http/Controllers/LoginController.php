<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function loginAdmin(Request $request)
    {
        $request->validate(['username' => 'required', 'password' => 'required']);

        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            // Gunakan key khusus 'admin_role' agar tidak tertimpa
            session([
                'admin_logged_in' => true,
                'admin_role' => 'admin'
            ]);
            return redirect('/admin/dashboard');
        }

        return back()->with('gagal', 'Login Admin gagal!');
    }

    public function loginOrtu(Request $request)
    {
        $request->validate(['nis' => 'required', 'password' => 'required']);

        $siswa = Siswa::where('nis', $request->nis)->first();
        
        if ($siswa && Hash::check($request->password, $siswa->password)) {
            // Gunakan key khusus 'ortu_xxx' agar tidak mengganggu session milik admin
            session([
                'ortu_logged_in' => true,
                'ortu_role' => 'orang_tua',
                'siswa_id' => $siswa->id,
                'nis' => $siswa->nis
            ]);
            return redirect('/ortu/dashboard');
        }

        return back()->with('gagal', 'NIS atau Password salah!');
    }

    // Pisahkan fungsi logout agar saat Ortu logout, Admin TIDAK ikut kelogout (dan sebaliknya)
    public function logoutAdmin(Request $request)
    {
        session()->forget(['admin_logged_in', 'admin_role']);
        Auth::logout(); 
        
        return redirect('/'); 
    }

    public function logoutOrtu(Request $request)
    {
        session()->forget(['ortu_logged_in', 'ortu_role', 'siswa_id', 'nis']);
        
        return redirect('/'); 
    }
}