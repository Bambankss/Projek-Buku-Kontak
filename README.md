# Buku Kontak v3 (Laravel 11/12)

Paket lengkap (sudah termasuk semua fitur v2). Fitur:

1. Tambah, edit, hapus kontak (+ tanggal lahir)
2. Search, filter kategori, urutan A-Z / Z-A, strip huruf A-Z yang bisa digeser
3. Info kontak (popup) dengan nomor telepon yang bisa langsung ditelepon
4. Notifikasi: berhasil ditambahkan / diubah / dihapus
5. Halaman utama (daftar kontak, avatar bulat ala Instagram)
6. Nomor darurat bawaan (Polisi, Ambulans, Damkar, dll), edit di `config/darurat.php`
7. Login, buat akun, profil user (sidebar + edit profil)
8. Backup (download JSON) dan restore (gabung atau ganti semua)
9. Navigasi Google Maps dari alamat kontak
10. Tanggal lahir + pengingat H-5 sampai hari H
11. Log notifikasi: lonceng + halaman Riwayat aktivitas (ada status Baru/dibaca)
12. Email: kolom email di kontak (bisa diklik untuk menulis email). Aplikasi tidak mengirim email.

Tabel: `users` (+ no_telepon, alamat), `categories`, `contacts` (+ tanggal_lahir), `activity_logs`.
Tiap akun hanya melihat data miliknya sendiri.

## Cara pasang

1. Buat project Laravel baru:
   ```
   composer create-project laravel/laravel buku-kontak
   cd buku-kontak
   ```
2. Salin isi folder ini ke dalam project (gabungkan folder, timpa file kalau diminta):
   - `app/` (Models, Http/Controllers, Services, Providers/AppServiceProvider.php menimpa bawaan)
   - `config/darurat.php`
   - `database/migrations/` (5 migration baru, jangan hapus migration bawaan Laravel)
   - `database/seeders/DemoSeeder.php`
   - `routes/web.php` (menimpa bawaan)
   - `resources/views/`
3. Di file `.env`, atur zona waktu dan bahasa supaya tanggal dan pengingat ulang tahun pas:
   ```
   APP_TIMEZONE=Asia/Jakarta
   APP_LOCALE=id
   ```
4. Jalankan:
   ```
   php artisan migrate
   php artisan db:seed --class=DemoSeeder   # opsional, data contoh untuk demo
   php artisan serve
   ```
5. Buka http://127.0.0.1:8000. Akun demo (kalau seeder dijalankan): `demo@example.com` / `password123`.

Seeder mengatur tanggal lahir beberapa kontak relatif terhadap hari ini, jadi pengingat ulang tahun langsung muncul di lonceng.
Jalankan seeder lagi kapan saja (misalnya sebelum demo) untuk menyegarkan tanggalnya.

Pengingat ulang tahun dihitung saat halaman dibuka, jadi tidak perlu scheduler atau cron.
Bootstrap dan Bootstrap Icons dimuat lewat CDN, jadi perlu internet saat demo.
