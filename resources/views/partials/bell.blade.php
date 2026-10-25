{{-- Lonceng notifikasi. Variabel $notifUlangTahun, $notifTerbaru, $notifBelumDibaca dikirim oleh AppServiceProvider --}}
<div class="dropdown">
    <button type="button" class="btn btn-sm btn-light border position-relative" data-bs-toggle="dropdown"
            data-bs-auto-close="outside" aria-label="Notifikasi">
        <i class="bi bi-bell"></i>
        @if ($notifBelumDibaca > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $notifBelumDibaca > 99 ? '99+' : $notifBelumDibaca }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0 notif-menu">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <strong class="small">Notifikasi</strong>
            @if ($notifBelumDibaca > 0)
                <form action="{{ route('logs.read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Tandai dibaca</button>
                </form>
            @endif
        </div>

        @if ($notifUlangTahun->isNotEmpty())
            <div class="px-3 pt-2 small text-muted fw-semibold">Ulang tahun 5 hari ke depan</div>
            @foreach ($notifUlangTahun as $item)
                <div class="notif-item">
                    <i class="bi bi-cake2 text-warning fs-5"></i>
                    <div>
                        <div class="fw-semibold">{{ $item->kontak->nama }}</div>
                        <div class="text-muted">
                            @if ($item->hari === 0)
                                <span class="text-danger fw-semibold">Hari ini!</span>
                            @else
                                {{ $item->hari }} hari lagi
                            @endif
                            &middot; {{ $item->tanggal->translatedFormat('j F') }} (ke-{{ $item->umur }})
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="px-3 pt-2 small text-muted fw-semibold">Aktivitas terbaru</div>
        @forelse ($notifTerbaru as $log)
            <div @class(['notif-item', 'unread' => $log->dibaca_pada === null])>
                <i class="bi {{ $log->ikon }} fs-5"></i>
                <div>
                    <div>{{ $log->pesan }}</div>
                    <div class="text-muted small">{{ $log->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="px-3 py-3 small text-muted">Belum ada aktivitas.</div>
        @endforelse

        <a href="{{ route('logs.index') }}" class="dropdown-item text-center small py-2">Lihat semua riwayat</a>
    </div>
</div>
