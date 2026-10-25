@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')
@section('heading', 'Riwayat Aktivitas')

@section('content')
<div class="content">
    <div class="scroll-area p-3 p-md-4">
        <div class="card shadow-sm mx-auto" style="max-width: 760px;">
            <div class="list-group list-group-flush">
                @forelse ($logs as $log)
                    <div class="list-group-item d-flex gap-3 align-items-start">
                        <i class="bi {{ $log->ikon }} fs-4"></i>
                        <div class="flex-grow-1">
                            <div>
                                {{ $log->pesan }}
                                @if ($log->dibaca_pada === null)
                                    <span class="badge text-bg-primary ms-1">Baru</span>
                                @endif
                            </div>
                            <div class="small text-muted">
                                {{ $log->created_at->translatedFormat('j F Y, H:i') }}
                                &middot; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-5">
                        <i class="bi bi-clock-history fs-1"></i>
                        <p class="mt-2 mb-0">Belum ada aktivitas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mx-auto mt-3" style="max-width: 760px;">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
