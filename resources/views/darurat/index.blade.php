@extends('layouts.app')

@section('title', 'Nomor Darurat')
@section('heading', 'Nomor Darurat')

@section('content')
<div class="content">
    <div class="scroll-area p-3 p-md-4">
        <div class="mx-auto" style="max-width: 900px;">
            <p class="text-muted">Nomor bawaan aplikasi. Tekan tombol telepon untuk langsung menelepon (di HP).</p>

            <div class="row g-3">
                @foreach ($daftar as $item)
                    <div class="col-12 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-circle text-bg-{{ $item['warna'] }} d-grid flex-shrink-0"
                                     style="width: 52px; height: 52px; place-items: center;">
                                    <i class="bi {{ $item['ikon'] }} fs-4"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold">{{ $item['nama'] }}</div>
                                    <div class="small text-muted">{{ $item['keterangan'] }}</div>
                                </div>
                                <a href="tel:{{ $item['nomor'] }}" class="btn btn-outline-{{ $item['warna'] }} fw-bold">
                                    <i class="bi bi-telephone"></i> {{ $item['nomor'] }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
