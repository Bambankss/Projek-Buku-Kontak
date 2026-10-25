<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('backup.index', [
            'jumlahKontak' => $user->contacts()->count(),
            'jumlahKategori' => $user->categories()->count(),
        ]);
    }

    // Download semua kontak + kategori milik akun ini sebagai file JSON
    public function unduh(Request $request)
    {
        $user = $request->user();

        $kontak = $user->contacts()->with('category')->orderByRaw('LOWER(nama)')->get()
            ->map(fn ($c) => [
                'nama' => $c->nama,
                'no_telepon' => $c->no_telepon,
                'email' => $c->email,
                'alamat' => $c->alamat,
                'tanggal_lahir' => $c->tanggal_lahir?->format('Y-m-d'),
                'kategori' => $c->category->nama ?? null,
            ])
            ->all();

        $data = [
            'aplikasi' => 'buku-kontak',
            'versi' => 1,
            'dibuat' => now()->toIso8601String(),
            'kategori' => $user->categories()->orderBy('nama')->pluck('nama')->all(),
            'kontak' => $kontak,
        ];

        ActivityLog::catat($user->id, 'backup', 'Backup diunduh (' . count($kontak) . ' kontak).');

        $namaFile = 'backup-kontak-' . now()->format('Y-m-d_His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $namaFile, ['Content-Type' => 'application/json']);
    }

    // Pulihkan dari file backup. Default: digabung (yang sudah ada dilewati). Opsional: ganti semua.
    public function pulihkan(Request $request)
    {
        $request->validate([
            'berkas' => ['required', 'file', 'max:2048', 'extensions:json'],
        ], [
            'berkas.required' => 'Pilih file backup terlebih dahulu.',
            'berkas.file' => 'Berkas tidak valid.',
            'berkas.max' => 'Ukuran file maksimal 2 MB.',
            'berkas.extensions' => 'File harus berformat .json.',
        ]);

        $isi = json_decode(file_get_contents($request->file('berkas')->getRealPath()), true);

        if (! is_array($isi) || ! isset($isi['kontak']) || ! is_array($isi['kontak'])) {
            return back()->withErrors(['berkas' => 'File ini bukan backup Buku Kontak yang valid.']);
        }

        $user = $request->user();
        $gantiSemua = $request->boolean('ganti_semua');

        [$masuk, $dilewati] = DB::transaction(function () use ($user, $isi, $gantiSemua) {
            if ($gantiSemua) {
                $user->contacts()->delete();
            }

            $kategori = $user->categories()->pluck('id', 'nama')->all(); // nama => id
            $masuk = 0;
            $dilewati = 0;

            foreach ($isi['kontak'] as $baris) {
                if (! is_array($baris)) {
                    $dilewati++;
                    continue;
                }

                $v = Validator::make($baris, [
                    'nama' => ['required', 'string', 'max:100'],
                    'no_telepon' => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
                    'email' => ['nullable', 'email', 'max:255'],
                    'alamat' => ['nullable', 'string', 'max:500'],
                    'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
                    'kategori' => ['nullable', 'string', 'max:50'],
                ]);

                if ($v->fails()) {
                    $dilewati++;
                    continue;
                }

                $d = $v->validated();
                $email = ! empty($d['email']) ? $d['email'] : null;

                // Dibungkus satu closure supaya orWhere tidak keluar dari batas user_id
                $sudahAda = $user->contacts()
                    ->where(function ($q) use ($d, $email) {
                        $q->where(fn ($w) => $w->where('nama', $d['nama'])->where('no_telepon', $d['no_telepon']));

                        if ($email) {
                            $q->orWhere('email', $email);
                        }
                    })
                    ->exists();

                if ($sudahAda) {
                    $dilewati++;
                    continue;
                }

                $namaKategori = trim($d['kategori'] ?? '') ?: 'Lainnya';
                if (! isset($kategori[$namaKategori])) {
                    $kategori[$namaKategori] = $user->categories()->create(['nama' => $namaKategori])->id;
                }

                $user->contacts()->create([
                    'category_id' => $kategori[$namaKategori],
                    'nama' => $d['nama'],
                    'no_telepon' => $d['no_telepon'],
                    'email' => $email,
                    'alamat' => $d['alamat'] ?? null,
                    'tanggal_lahir' => ! empty($d['tanggal_lahir']) ? $d['tanggal_lahir'] : null,
                ]);
                $masuk++;
            }

            return [$masuk, $dilewati];
        });

        $pesan = "Restore selesai: {$masuk} kontak ditambahkan, {$dilewati} dilewati.";
        ActivityLog::catat($user->id, 'restore', $pesan . ($gantiSemua ? ' (mode ganti semua)' : ''));

        return redirect()->route('backup.index')->with('success', $pesan);
    }
}
