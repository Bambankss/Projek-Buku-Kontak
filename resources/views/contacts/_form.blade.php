@csrf

<div class="mb-3">
    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
    <input type="text" id="nama" name="nama" value="{{ old('nama', $contact->nama ?? '') }}"
           class="form-control @error('nama') is-invalid @enderror" autofocus>
    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="no_telepon" class="form-label">Nomor telepon <span class="text-danger">*</span></label>
    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $contact->no_telepon ?? '') }}"
           placeholder="08123456789" class="form-control @error('no_telepon') is-invalid @enderror">
    @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $contact->email ?? '') }}"
           class="form-control @error('email') is-invalid @enderror">
    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">-- Pilih kategori --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('category_id', $contact->category_id ?? '') === (string) $cat->id)>
                    {{ $cat->nama }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#kategoriModal">
            <i class="bi bi-plus-lg"></i> Kategori baru
        </button>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

@php
    $tanggalLahir = isset($contact) && $contact->tanggal_lahir ? $contact->tanggal_lahir->format('Y-m-d') : '';
@endphp
<div class="mb-3">
    <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
    <input type="date" id="tanggal_lahir" name="tanggal_lahir" max="{{ now()->format('Y-m-d') }}"
           value="{{ old('tanggal_lahir', $tanggalLahir) }}"
           class="form-control @error('tanggal_lahir') is-invalid @enderror">
    @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <div class="form-text">Opsional. Kalau diisi, kamu diingatkan 5 hari sebelum ulang tahunnya.</div>
</div>

<div class="mb-3">
    <label for="alamat" class="form-label">Alamat</label>
    <textarea id="alamat" name="alamat" rows="3"
              class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $contact->alamat ?? '') }}</textarea>
    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<a href="{{ route('contacts.index') }}" class="btn btn-secondary">Batal</a>
<button type="submit" class="btn btn-primary">Simpan</button>
