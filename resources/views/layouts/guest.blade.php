<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Buku Kontak')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #f3e8ff 0%, #e0f2fe 100%); }
        .brand-ring {
            width: 72px; height: 72px; padding: 3px; border-radius: 50%;
            background: linear-gradient(45deg, #f9ce34, #ee2a7b, #6228d7);
        }
        .brand-ring > div {
            width: 100%; height: 100%; border-radius: 50%; background: #fff;
            display: grid; place-items: center; font-size: 30px;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <main class="container" style="max-width: 460px;">
        <div class="text-center mb-4">
            <div class="brand-ring mx-auto mb-2"><div>📒</div></div>
            <h1 class="h4 mb-0">Buku Kontak</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                @yield('content')
            </div>
        </div>
    </main>
</body>
</html>
