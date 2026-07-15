@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran TK Mutiara')
@section('page_title', 'Verifikasi Pembayaran')

@section('content')
    {{-- HAPUS SPINNER BAWAAN BROWSER --}}
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <p class="text-xs text-slate-400 -mt-5 mb-6">Sistem Informasi Pembayaran Keuangan Sekolah</p>

    @if(session('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold flex items-center gap-2">
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if(session('gagal'))
        <div class="mb-4 p-4 text-sm text-red-800 bg-red-50 rounded-xl border border-red-100 font-semibold flex items-center gap-2">
            <span>{{ session('gagal') }}</span>
        </div>
    @endif

    <div class="flex gap-2 mb-6">
        @foreach(['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
            <a href="{{ route('pembayaran.index', ['status' => $key]) }}" 
               class="px-5 py-2 rounded-full text-sm font-semibold 
               {{ (request('status') ?? 'semua') == $key ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600' }}">
               {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tabelVerifikasi">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Siswa</th>
                        <th class="py-4 px-6">Kelas</th>
                        <th class="py-4 px-6">Jenis Tagihan</th>
                        <th class="py-4 px-6">Nominal</th>
                        <th class="py-4 px-6">Bukti</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-100">
                    @forelse($pembayarans as $pembayaran)
                        <tr class="baris-verifikasi hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-slate-900 font-semibold">{{ $pembayaran->siswa->nama ?? '-' }}</td>
                            <td class="py-4 px-6">{{ $pembayaran->siswa->kelas ?? '-' }}</td>
                            <td class="py-4 px-6">{{ $pembayaran->nama_iuran }}</td>
                            <td class="py-4 px-6 text-slate-900 font-semibold">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                @if(str_contains(strtolower($pembayaran->nama_iuran), 'program'))
                                    <div class="text-[10px] text-slate-400 font-normal">
                                        Sudah dibayar: Rp {{ number_format($pembayaran->pembayarans->sum('jumlah_diterima'), 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            
                            <td class="py-4 px-6">
                                @php
                                    $bukti = $pembayaran->pembayarans->last()->bukti_bayar ?? $pembayaran->bukti_bayar;
                                @endphp

                                @if(!empty($bukti))
                                    <a href="javascript:void(0)" onclick="openModal('{{ asset('storage/' . $bukti) }}')" class="text-blue-600 hover:underline font-bold text-xs">Lihat Bukti</a>
                                @else
                                    <span class="text-slate-400 text-xs italic">Belum ada</span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg border border-rose-100">{{ $pembayaran->status_tagihan }}</span>
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                            @if($pembayaran->status_tagihan != 'Lunas' && (!empty($pembayaran->bukti_bayar) || $pembayaran->pembayarans->count() > 0))
                                
                                @if(str_contains(strtolower($pembayaran->nama_iuran), 'program'))
                                    <form action="{{ route('pembayaran.konfirmasi', $pembayaran->id_detail) }}" method="POST" class="flex flex-col gap-1 items-center">
                                        @csrf
                                        {{-- Input Nominal: spinner di-hide lewat CSS, min="1" ditambahkan untuk pengamanan HTML5 --}}
                                        <input type="number" name="jumlah_diterima" min="1" class="input-nominal-cicil w-24 border border-slate-300 rounded p-1 text-[10px] text-center focus:outline-none focus:border-emerald-600" placeholder="Nominal" required>
                                        <button type="submit" class="w-24 bg-emerald-600 hover:bg-emerald-700 text-white py-1 rounded text-[10px] font-bold transition cursor-pointer">CICIL</button>
                                    </form>
                                @else
                                    <form action="{{ route('pembayaran.konfirmasi', $pembayaran->id_detail) }}" method="POST">
                                        @csrf
                                        @php
                                            $sudahBayar = $pembayaran->pembayarans->sum('jumlah_diterima');
                                            $sisaTagihan = $pembayaran->jumlah_bayar - $sudahBayar;
                                        @endphp
                                        <input type="hidden" name="jumlah_diterima" value="{{ $sisaTagihan }}">
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-[10px] font-bold hover:bg-blue-700 transition cursor-pointer">LUNAS</button>
                                    </form>
                                @endif
                                
                                <button type="button" onclick="document.getElementById('modal-tolak-{{ $pembayaran->id_detail }}').classList.remove('hidden')" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-[10px] font-bold mt-1 cursor-pointer">TOLAK</button>

                            @elseif($pembayaran->status_tagihan == 'Lunas')
                                <span class="text-emerald-600 font-bold text-[10px] bg-emerald-50 px-2 py-1 rounded">SUDAH LUNAS</span>
                            @else
                                <span class="text-slate-300 text-[10px] italic">Menunggu upload...</span>
                            @endif
                            </td>
                        </tr>

                        <div id="modal-tolak-{{ $pembayaran->id_detail }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white p-6 rounded-2xl w-80 shadow-2xl">
                                <h3 class="font-bold text-slate-800 mb-4">Alasan Penolakan</h3>
                                <form action="{{ route('pembayaran.tolak', $pembayaran->id_detail) }}" method="POST">
                                    @csrf
                                    <textarea name="alasan" class="w-full border border-slate-200 rounded-xl p-3 mb-4 text-sm" rows="3" required placeholder="Tulis alasan..."></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="document.getElementById('modal-tolak-{{ $pembayaran->id_detail }}').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-lg text-sm cursor-pointer">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm cursor-pointer">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-slate-400">Tidak ada data tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="modalPreview" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4" onclick="closeModal()">
        <div class="relative max-w-4xl w-full">
            <button onclick="closeModal()" class="absolute -top-10 right-0 text-white font-bold text-xl cursor-pointer">TUTUP [X]</button>
            <img id="gambarPreview" src="" class="w-full h-auto rounded-lg shadow-2xl">
        </div>
    </div>

    <script>
        function openModal(url) {
            document.getElementById('gambarPreview').src = url;
            document.getElementById('modalPreview').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('modalPreview').classList.add('hidden');
        }

        // BLOKIR MANUAL KETIKAN MINUS (-), PLUS (+), DAN HURUF 'E'
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.input-nominal-cicil');
            inputs.forEach(input => {
                input.addEventListener('keydown', function(e) {
                    if (['e', 'E', '-', '+', ',', '.'].includes(e.key)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection