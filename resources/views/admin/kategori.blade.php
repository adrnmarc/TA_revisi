@extends('layouts.admin')
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Kelola Kategori Tagihan</h2>
    
    {{-- Form Tambah Kategori --}}
    <form action="{{ route('kategori.store') }}" method="POST" class="bg-white p-4 rounded shadow mb-6">
        @csrf
        <div class="grid grid-cols-4 gap-4">
            <input type="text" name="nama_kategori" placeholder="Nama Kategori" class="border p-2 rounded" required>
            <input type="number" name="harga_default" placeholder="Harga Default" class="border p-2 rounded" required>
            <input type="number" name="maksimal_angsuran" placeholder="Maks Angsuran" class="border p-2 rounded" required>
            <button type="submit" class="bg-blue-600 text-white p-2 rounded">Simpan</button>
        </div>
    </form>

    {{-- Tabel Daftar Kategori --}}
    <table class="w-full bg-white rounded shadow">
        <tr class="bg-slate-100">
            <th class="p-3">Nama</th>
            <th class="p-3">Harga</th>
            <th class="p-3">Maks Cicilan</th>
            <th class="p-3">Aksi</th>
        </tr>
        @foreach($kategoris as $k)
        <tr>
            <td class="p-3 border-t">{{ $k->nama_kategori }}</td>
            <td class="p-3 border-t">Rp {{ number_format($k->harga_default) }}</td>
            <td class="p-3 border-t">{{ $k->maksimal_angsuran }}x</td>
            <td class="p-3 border-t">
                <form action="{{ route('kategori.destroy', $k->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-red-500">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection