@extends('layouts.app')

@section('title', 'Daftar Kontak')
@section('heading', 'Kontak')

{{-- Pojok kanan atas: search, filter kategori, urutan A-Z, tombol tambah --}}
@section('toolbar')
    <form method="GET" action="{{ route('contacts.index') }}" class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
        <input type="hidden" name="urut" value="{{ $urut }}">

        <div class="input-group input-group-sm" style="width: 210px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kontak..." aria-label="Cari kontak">
        </div>

        <select name="kategori" class="form-select form-select-sm" style="width: auto;" aria-label="Filter kategori"
                onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) request('kategori') === (string) $cat->id)>{{ $cat->nama }}</option>
            @endforeach
        </select>

        @if (request()->filled('q') || request()->filled('kategori'))
            <a href="{{ route('contacts.index', ['urut' => $urut]) }}" class="btn btn-sm btn-link text-decoration-none">Reset</a>
        @endif
    </form>

    <a href="{{ request()->fullUrlWithQuery(['urut' => $urut === 'asc' ? 'desc' : 'asc']) }}"
       class="btn btn-sm btn-outline-secondary" title="Balik urutan">
        <i class="bi bi-sort-alpha-{{ $urut === 'asc' ? 'down' : 'up' }}"></i> {{ $urut === 'asc' ? 'A-Z' : 'Z-A' }}
    </a>

    <a href="{{ route('contacts.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah
    </a>
@endsection

@section('content')
<div class="content">
    <div class="scroll-area" id="scrollArea">
        @forelse ($groups as $huruf => $items)
            <section data-section="{{ $huruf }}">
                <div class="huruf-header">{{ $huruf }}</div>

                @foreach ($items as $contact)
                    <div class="contact-row">
                        {{-- Avatar bulat --}}
                        <div class="ring">
                            <div class="avatar" style="background: {{ $contact->warna }};">{{ $contact->inisial }}</div>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $contact->nama }}</div>
                            <div class="small text-muted text-truncate">{{ $contact->no_telepon }}</div>
                            @php $hariUltah = $contact->hariMenujuUlangTahun(); @endphp
                            @if ($hariUltah !== null)
                                <span class="badge text-bg-warning">
                                    <i class="bi bi-cake2"></i> {{ $hariUltah === 0 ? 'Ulang tahun hari ini' : 'Ulang tahun ' . $hariUltah . ' hari lagi' }}
                                </span>
                            @endif
                        </div>

                        <span class="badge rounded-pill text-bg-light border d-none d-sm-inline">
                            {{ $contact->category->nama ?? '-' }}
                        </span>

                        {{-- Titik tiga --}}
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown"
                                    data-bs-popper-config='{"strategy":"fixed"}' aria-label="Menu kontak {{ $contact->nama }}">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button type="button" class="dropdown-item"
                                            data-bs-toggle="modal" data-bs-target="#infoModal"
                                            data-nama="{{ $contact->nama }}"
                                            data-inisial="{{ $contact->inisial }}"
                                            data-warna="{{ $contact->warna }}"
                                            data-telepon="{{ $contact->no_telepon }}"
                                            data-email="{{ $contact->email }}"
                                            data-alamat="{{ $contact->alamat }}"
                                            data-lahir="{{ $contact->tanggal_lahir?->translatedFormat('j F Y') }}"
                                            data-kategori="{{ $contact->category->nama ?? '' }}">
                                        <i class="bi bi-info-circle me-2"></i>Info kontak
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('contacts.edit', $contact) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
                                          onsubmit="return confirm('Hapus kontak {{ $contact->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            </section>
        @empty
            <div class="text-center text-muted py-5 px-3">
                <i class="bi bi-person-lines-fill fs-1"></i>
                @if (request()->filled('q') || request()->filled('kategori'))
                    <p class="mt-2 mb-0">Tidak ada kontak yang cocok.</p>
                @else
                    <p class="mt-2">Belum ada kontak.</p>
                    <a href="{{ route('contacts.create') }}" class="btn btn-primary btn-sm">Tambah kontak pertama</a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Strip huruf A-Z --}}
    @if ($total > 0)
        <div class="index-strip" id="indexStrip" aria-hidden="true">
            @foreach ($deret as $h)
                <span data-huruf="{{ $h }}" class="{{ $groups->has($h) ? 'on' : '' }}">{{ $h }}</span>
            @endforeach
        </div>
        <div class="index-bubble" id="indexBubble"></div>
    @endif
</div>

{{-- Popup info kontak (satu modal dipakai bersama, diisi lewat JS) --}}
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h2 class="modal-title h6 text-muted" id="infoModalLabel">Info kontak</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="text-center mb-3">
                    <div class="ring ring-lg mx-auto"><div class="avatar" id="infoAvatar"></div></div>
                    <h3 class="h5 mt-3 mb-1" id="infoNama"></h3>
                    <span class="badge rounded-pill text-bg-light border" id="infoKategori"></span>
                </div>
                <dl class="mb-0">
                    <dt class="text-muted fw-normal small"><i class="bi bi-telephone"></i> Nomor telepon</dt>
                    <dd><a href="#" id="infoTelepon"></a></dd>
                    <dt class="text-muted fw-normal small"><i class="bi bi-envelope"></i> Email</dt>
                    <dd id="infoEmail"></dd>
                    <dt class="text-muted fw-normal small"><i class="bi bi-cake2"></i> Tanggal lahir</dt>
                    <dd id="infoLahir"></dd>
                    <dt class="text-muted fw-normal small"><i class="bi bi-geo-alt"></i> Alamat</dt>
                    <dd id="infoAlamat"></dd>
                </dl>
                <a href="#" id="infoMaps" class="btn btn-outline-primary btn-sm d-none" target="_blank" rel="noopener">
                    <i class="bi bi-signpost-split"></i> Navigasi Google Maps
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1) Isi popup info dari data-* tombol yang diklik
    (function () {
        var modal = document.getElementById('infoModal');
        modal.addEventListener('show.bs.modal', function (ev) {
            var d = ev.relatedTarget.dataset;
            var avatar = document.getElementById('infoAvatar');
            avatar.textContent = d.inisial;
            avatar.style.background = d.warna;
            document.getElementById('infoNama').textContent = d.nama;

            var kat = document.getElementById('infoKategori');
            kat.textContent = d.kategori;
            kat.style.display = d.kategori ? '' : 'none';

            var tel = document.getElementById('infoTelepon');
            tel.textContent = d.telepon;
            tel.href = 'tel:' + d.telepon;

            var email = document.getElementById('infoEmail');
            email.textContent = '';
            if (d.email) {
                var a = document.createElement('a');
                a.href = 'mailto:' + d.email;
                a.textContent = d.email;
                email.appendChild(a);
            } else {
                email.textContent = '-';
            }

            document.getElementById('infoLahir').textContent = d.lahir || '-';
            document.getElementById('infoAlamat').textContent = d.alamat || '-';

            // Tombol navigasi hanya muncul kalau alamat ada; membuka rute Google Maps ke alamat itu
            var maps = document.getElementById('infoMaps');
            if (d.alamat) {
                maps.href = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(d.alamat);
                maps.classList.remove('d-none');
            } else {
                maps.classList.add('d-none');
            }
        });
    })();

    // 2) Strip A-Z: tekan atau geser (drag) untuk loncat ke huruf
    (function () {
        var strip = document.getElementById('indexStrip');
        if (!strip) return;

        var area = document.getElementById('scrollArea');
        var bubble = document.getElementById('indexBubble');
        var dragging = false, last = null, hideTimer = null;

        function jumpTo(e) {
            var el = document.elementFromPoint(e.clientX, e.clientY);
            var item = el && el.closest ? el.closest('[data-huruf]') : null;
            if (!item || !strip.contains(item) || !item.classList.contains('on')) return;

            var huruf = item.dataset.huruf;
            if (huruf === last) return;
            last = huruf;

            var section = area.querySelector('[data-section="' + huruf + '"]');
            if (section) area.scrollTop = section.offsetTop;

            bubble.textContent = huruf;
            bubble.classList.add('show');
            clearTimeout(hideTimer);
        }

        function stop() {
            dragging = false;
            last = null;
            clearTimeout(hideTimer);
            hideTimer = setTimeout(function () { bubble.classList.remove('show'); }, 400);
        }

        strip.addEventListener('pointerdown', function (e) {
            dragging = true;
            strip.setPointerCapture(e.pointerId);
            jumpTo(e);
        });
        strip.addEventListener('pointermove', function (e) { if (dragging) jumpTo(e); });
        strip.addEventListener('pointerup', stop);
        strip.addEventListener('pointercancel', stop);
    })();
</script>
@endpush
