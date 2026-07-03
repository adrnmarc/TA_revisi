<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Konfirmasi Pembayaran</h4>
                    
                    {{-- Detail Tagihan --}}
                    <div class="mb-4">
                        <p class="text-muted mb-1">Tagihan:</p>
                        <h5 class="fw-bold text-slate-800">{{ $tagihan->nama_iuran ?? 'Data tidak ditemukan' }}</h5>
                        <h3 class="text-primary fw-bold">
                            Rp {{ isset($tagihan) ? number_format($tagihan->jumlah_bayar, 0, ',', '.') : '0' }}
                        </h3>
                    </div>
                    
                    {{-- Instruksi Pembayaran --}}
                    <div class="alert alert-info border-0 mb-4" style="background-color: #e3f2fd;">
                        <h6 class="fw-bold text-blue-800 mb-2">Instruksi Pembayaran:</h6>
                        <p class="small mb-2">Silakan transfer ke nomor rekening berikut:</p>
                        <ul class="small list-unstyled mb-0">
                            <li><strong>Bank:</strong> BNI</li>
                            <li><strong>No. Rek:</strong> 1234567890</li>
                            <li><strong>Atas Nama:</strong> TK Mutiara</li>
                        </ul>
                    </div>

                    {{-- Form Upload --}}
                    <form action="{{ url('ortu/bayar/' . ($tagihan->id_detail ?? $tagihan->id)) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Bukti Transfer (Foto/Screenshot):</label>
                            <input type="file" name="bukti" class="form-control" accept="image/*" required>
                            <small class="text-muted">Pastikan bukti foto jelas terbaca.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Kirim Bukti Pembayaran</button>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="/ortu/tagihan" class="text-secondary text-decoration-none small">Kembali ke Daftar Tagihan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>