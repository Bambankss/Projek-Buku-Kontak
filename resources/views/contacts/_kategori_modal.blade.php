{{-- Popup tambah kategori. Tidak memakai <form> karena letaknya di dalam halaman form kontak --}}
<div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h6" id="kategoriModalLabel">Kategori baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <label for="kategoriNama" class="form-label">Nama kategori</label>
                <input type="text" id="kategoriNama" class="form-control" maxlength="50" placeholder="Contoh: Komunitas">
                <div class="invalid-feedback" id="kategoriError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="kategoriSimpan">Simpan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simpan kategori lewat fetch(), lalu langsung dimasukkan ke dropdown dan dipilih
    (function () {
        var modalEl = document.getElementById('kategoriModal');
        var input = document.getElementById('kategoriNama');
        var error = document.getElementById('kategoriError');
        var button = document.getElementById('kategoriSimpan');
        var select = document.getElementById('category_id');
        var token = document.querySelector('meta[name="csrf-token"]').content;

        function showError(msg) {
            error.textContent = msg;
            input.classList.add('is-invalid');
        }

        modalEl.addEventListener('shown.bs.modal', function () { input.focus(); });
        modalEl.addEventListener('hidden.bs.modal', function () {
            input.value = '';
            input.classList.remove('is-invalid');
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); button.click(); }
        });

        button.addEventListener('click', function () {
            input.classList.remove('is-invalid');
            button.disabled = true;

            fetch('{{ route('categories.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ nama: input.value })
            })
            .then(function (res) {
                return res.json().then(function (data) { return { ok: res.ok, data: data }; });
            })
            .then(function (r) {
                if (!r.ok) {
                    var msg = (r.data.errors && r.data.errors.nama && r.data.errors.nama[0]) || 'Gagal menyimpan kategori.';
                    showError(msg);
                    return;
                }
                var opt = document.createElement('option');
                opt.value = r.data.id;
                opt.textContent = r.data.nama;
                select.appendChild(opt);
                select.value = r.data.id;
                select.classList.remove('is-invalid');
                bootstrap.Modal.getInstance(modalEl).hide();
            })
            .catch(function () { showError('Tidak bisa terhubung ke server.'); })
            .finally(function () { button.disabled = false; });
        });
    })();
</script>
@endpush
