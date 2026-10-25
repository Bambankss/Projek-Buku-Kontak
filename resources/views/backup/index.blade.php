@extends('layouts.app')

@section('title', 'Backup & Restore')
@section('heading', 'Backup & Restore')

@section('content')
<div class="content">
    <div class="scroll-area p-3 p-md-4">
        <div class="mx-auto" style="max-width: 640px;">

            {{-- Backup --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6"><i class="bi bi-cloud-arrow-down me-1"></i> Backup</h2>
                    <p class="text-muted small mb-3">
                        Unduh semua kontak dan kategori milikmu sebagai file <code>.json</code>.
                        Saat ini ada <strong>{{ $jumlahKontak }}</strong> kontak dan <strong>{{ $jumlahKategori }}</strong> kategori.
                    </p>
                    <a href="{{ route('backup.download') }}" class="btn btn-primary">
                        <i class="bi bi-download"></i> Unduh backup
                    </a>
                </div>
            </div>

            {{-- Restore --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6"><i class="bi bi-cloud-arrow-up me-1"></i> Restore</h2>
                    <p class="text-muted small">
                        Pilih file backup <code>.json</code>. Kontak yang sudah ada (nama + nomor sama, atau email sama) dilewati,
                        kategori yang belum ada dibuat otomatis.
                    </p>

                    <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                          onsubmit="if (this.ganti_semua.checked) { return confirm('Semua kontak yang ada sekarang akan DIHAPUS lalu diganti isi file. Lanjutkan?'); }">
                        @csrf

                        <div class="mb-3">
                            <input type="file" name="berkas" accept=".json,application/json"
                                   class="form-control @error('berkas') is-invalid @enderror">
                            @error('berkas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="ganti_semua" id="ganti_semua" value="1">
                            <label class="form-check-label" for="ganti_semua">
                                Ganti semua <span class="text-muted small">(hapus kontak yang ada sekarang dulu)</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-upload"></i> Pulihkan
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
