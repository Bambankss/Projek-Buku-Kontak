@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<h2 class="h5 mb-3">Masuk ke akunmu</h2>

<form action="{{ route('login.attempt') }}" method="POST" novalidate>
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <div class="input-group has-validation">
        <input type="password" id="password" name="password" autocomplete="current-password"
               class="form-control @error('password') is-invalid @enderror">
        <button type="button" class="btn btn-outline-secondary" data-toggle-password="password"
                aria-label="Tampilkan password">
            <i class="bi bi-eye"></i>
        </button>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">Ingat saya</label>
    </div>

    <button type="submit" class="btn btn-primary w-100">Masuk</button>
</form>

<p class="text-center small mt-3 mb-0">
    Belum punya akun? <a href="{{ route('register') }}">Buat akun</a>
</p>

<script>
    // Tombol mata: ganti tampilan password antara tersembunyi dan terlihat
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.togglePassword);
            var icon = btn.querySelector('i');
            var tampil = input.type === 'password';

            input.type = tampil ? 'text' : 'password';
            icon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
</script>
@endsection
