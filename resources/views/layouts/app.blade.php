<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Buku Kontak')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        html, body { 
            height: 100%;
        }

        body { 
            margin: 0;
            overflow: hidden;
            background: #f8f9fa;
        }

        /* Layar dibagi: sidebar (1/3) di kiri + layar utama di kanan, bukan saling menimpa */
        .app { 
            display: flex;
            height: 100vh;
            height: 100dvh;
        }

        .sidebar {
            width: 0;
            flex-shrink: 0;
            overflow: hidden;
            background: #fff;
            border-right: 1px solid #dee2e6;
            transition: width .25s ease;
        }

        body.sidebar-open .sidebar { 
            width: 33.3333%; 
        }

        .sidebar-inner {
            min-width: 260px;
            height: 100%;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .main { 
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
    
        .topbar {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            padding: .75rem 1rem;
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        .content { 
            flex: 1;
            min-height: 0;
            position: relative;
            display: flex;
        }

        .scroll-area { 
            flex: 1;
            overflow-y: auto;
            position: relative;
        }

        /* HP: sidebar menutupi layar penuh karena 1/3 terlalu sempit */
        @media (max-width: 767.98px) {
            body.sidebar-open .sidebar { 
                position: fixed;
                inset: 0;
                width: 100%;
                z-index: 1040; 
            }
        }

        /* Avatar bulat ala Instagram */
        .ring {
            flex-shrink: 0;
            padding: 2px; 
            border-radius: 50%;
            background: linear-gradient(45deg, #f9ce34, #ee2a7b, #6228d7);
        }
        .avatar {
            width: 46px; 
            height: 46px; 
            border-radius: 50%; 
            border: 2px solid #fff;
            display: grid; 
            place-items: center; 
            color: #fff; 
            font-weight: 600; 
            font-size: 1.1rem;
        }
        .ring-lg .avatar { width: 88px; height: 88px; font-size: 2rem; }

        /* Daftar kontak */
        .huruf-header {
            position: sticky; top: 0; z-index: 2; padding: .25rem 1rem;
            background: #eef0f3; color: #6c757d; font-weight: 600; font-size: .85rem;
        }
        .contact-row {
            display: flex; align-items: center; gap: .85rem;
            padding: .6rem 2.25rem .6rem 1rem; background: #fff; border-bottom: 1px solid #f1f3f5;
        }
        .contact-row:hover { background: #f8f9ff; }
        .min-w-0 { min-width: 0; }

        /* Strip A-Z di pinggir kanan: tekan atau geser untuk loncat ke huruf */
        .index-strip {
            position: absolute; top: 8px; bottom: 8px; right: 14px; z-index: 5;
            display: flex; flex-direction: column; justify-content: space-between; align-items: center;
            width: 20px; border-radius: 10px; background: rgba(255,255,255,.7);
            font-size: 10px; line-height: 1; user-select: none; touch-action: none; cursor: pointer;
        }
        .index-strip span { color: #ced4da; padding: 1px 0; }
        .index-strip span.on { color: #0d6efd; font-weight: 700; }
        .index-bubble {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 6;
            width: 84px; height: 84px; border-radius: 50%; background: rgba(33,37,41,.8); color: #fff;
            display: grid; place-items: center; font-size: 2.5rem; font-weight: 700;
            opacity: 0; transition: opacity .15s; pointer-events: none;
        }
        .index-bubble.show { opacity: 1; }

        /* Lonceng notifikasi */
        .notif-menu { width: 340px; max-width: 92vw; max-height: 75vh; overflow-y: auto; }
        .notif-item { display: flex; gap: .6rem; padding: .55rem .9rem; border-bottom: 1px solid #f1f3f5; font-size: .875rem; }
        .notif-item.unread { background: #f3f7ff; }
    </style>
</head>
<body>
<div class="app">

    {{-- Sidebar profil + menu (kiri) --}}
    <aside class="sidebar" id="sidebar" aria-label="Profil dan menu">
        <div class="sidebar-inner">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 text-uppercase text-muted mb-0">Profil</h2>
                <button type="button" class="btn-close" id="sidebarClose" aria-label="Tutup"></button>
            </div>

            <div class="text-center mb-3">
                <div class="ring ring-lg mx-auto">
                    <div class="avatar" style="background: #6f42c1;">{{ auth()->user()->inisial }}</div>
                </div>
                <h3 class="h5 mt-3 mb-0">{{ auth()->user()->name }}</h3>
            </div>

            <div class="list-group list-group-flush mb-3">
                <a href="{{ route('contacts.index') }}" @class(['list-group-item list-group-item-action', 'active' => request()->routeIs('contacts.*')])>
                    <i class="bi bi-people me-2"></i>Kontak
                </a>
                <a href="{{ route('darurat.index') }}" @class(['list-group-item list-group-item-action', 'active' => request()->routeIs('darurat.*')])>
                    <i class="bi bi-telephone-fill text-danger me-2"></i>Nomor darurat
                </a>
                <a href="{{ route('logs.index') }}" @class(['list-group-item list-group-item-action', 'active' => request()->routeIs('logs.*')])>
                    <i class="bi bi-clock-history me-2"></i>Riwayat aktivitas
                </a>
                <a href="{{ route('backup.index') }}" @class(['list-group-item list-group-item-action', 'active' => request()->routeIs('backup.*')])>
                    <i class="bi bi-cloud-arrow-down me-2"></i>Backup &amp; restore
                </a>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <h4 class="h6 text-muted mb-0">Informasi akun</h4>
                <a href="{{ route('profile.edit') }}" class="small text-decoration-none"><i class="bi bi-pencil"></i> Edit</a>
            </div>
            <dl class="small my-3">
                <dt class="text-muted fw-normal"><i class="bi bi-envelope"></i> Email</dt>
                <dd class="text-break">{{ auth()->user()->email }}</dd>
                <dt class="text-muted fw-normal"><i class="bi bi-telephone"></i> Nomor telepon</dt>
                <dd>{{ auth()->user()->no_telepon ?? '-' }}</dd>
                <dt class="text-muted fw-normal"><i class="bi bi-geo-alt"></i> Alamat</dt>
                <dd>{{ auth()->user()->alamat ?? '-' }}</dd>
            </dl>

            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Layar utama (kanan) --}}
    <div class="main">
        <header class="topbar">
            <button type="button" class="btn btn-light border" id="sidebarToggle" aria-label="Buka profil dan menu">
                <i class="bi bi-person-circle"></i>
            </button>
            <h1 class="h5 mb-0">@yield('heading', 'Kontak')</h1>
            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap justify-content-end">
                @yield('toolbar')

                <a href="{{ route('darurat.index') }}" class="btn btn-sm btn-outline-danger" title="Nomor darurat" aria-label="Nomor darurat">
                    <i class="bi bi-telephone-fill"></i>
                </a>
                @include('partials.bell')
            </div>
        </header>

        @if (session('success'))
            <div class="px-3 pt-3">
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Buka/tutup sidebar profil; statusnya diingat selama browser sama
    (function () {
        var body = document.body;
        function setOpen(open) {
            body.classList.toggle('sidebar-open', open);
            try { localStorage.setItem('sidebarOpen', open ? '1' : '0'); } catch (e) {}
        }
        try {
            if (localStorage.getItem('sidebarOpen') === '1' && window.innerWidth >= 768) {
                body.classList.add('sidebar-open');
            }
        } catch (e) {}
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            setOpen(!body.classList.contains('sidebar-open'));
        });
        document.getElementById('sidebarClose').addEventListener('click', function () {
            setOpen(false);
        });
    })();
</script>
@stack('scripts')
</body>
</html>
