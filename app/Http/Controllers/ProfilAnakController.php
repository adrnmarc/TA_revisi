<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

class ProfilAnakController extends Controller
{
   public function index()
{
   
    $nis = session('nis');
    if (!$nis) return "Sesi habis, silakan login ulang.";
    
    $siswa = \App\Models\Siswa::where('nis', $nis)->first();

    session(['siswa' => $siswa]);

    return view('ortu.profil_anak', compact('siswa'));
}
}