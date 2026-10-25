@extends('layouts.guest')

@section('title', 'Buat Akun')

@section('content')
<h2 class="h5 mb-3">Buat akun baru</h2>

<form action="{{ route('register.store') }}" method="POST" novalidate>
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" autofocus
               class="form-control @error('name') is-invalid @enderror">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="no_telepon" class="form-label">Nomor telepon</label>
        <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" autocomplete="tel"
               placeholder="08123456789" class="form-control @error('no_telepon') is-invalid @enderror">
        @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <textarea id="alamat" name="alamat" rows="2" autocomplete="street-address"
                  class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
        @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <div class="input-group has-validation">
        <input type="password" id="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror">
        <button type="button" class="btn btn-outline-secondary" data-toggle-password="password"
                aria-label="Tampilkan password">
            <i class="bi bi-eye"></i>
        </button>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-text">Minimal 8 karakter.</div>
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">Ulangi password</label>
    <div class="input-group">
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
               class="form-control">
        <button type="button" class="btn btn-outline-secondary" data-toggle-password="password_confirmation"
                aria-label="Tampilkan password">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>

    <button type="submit" class="btn btn-primary w-100">Buat akun</button>
</form>

<p class="text-center small mt-3 mb-0">
    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
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
