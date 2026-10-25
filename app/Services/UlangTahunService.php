<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Collection;

class UlangTahunService
{
    // Kontak yang ulang tahunnya dalam $hari hari ke depan (termasuk hari ini), diurutkan dari yang terdekat
    public function mendatang(User $user, int $hari = 5): Collection
    {
        $hariIni = today();

        return $user->contacts()
            ->whereNotNull('tanggal_lahir')
            ->get()
            ->map(function (Contact $kontak) use ($hariIni) {
                $berikut = $kontak->ulangTahunBerikutnya($hariIni);

                return (object) [
                    'kontak' => $kontak,
                    'tanggal' => $berikut,
                    'hari' => (int) $hariIni->diffInDays($berikut),
                    'umur' => $berikut->year - $kontak->tanggal_lahir->year,
                ];
            })
            ->filter(fn ($item) => $item->hari <= $hari)
            ->sortBy('hari')
            ->values();
    }

    // Simpan pengingat ke riwayat, sekali saja per kontak per tahun (dijaga kolom 'kunci' yang unik)
    public function catat(User $user, Collection $daftar): void
    {
        foreach ($daftar as $item) {
            ActivityLog::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'kunci' => 'ultah-' . $item->kontak->id . '-' . $item->tanggal->year,
                ],
                [
                    'jenis' => 'ulang_tahun',
                    'pesan' => 'Ulang tahun ke-' . $item->umur . ' ' . $item->kontak->nama
                        . ' pada ' . $item->tanggal->translatedFormat('j F Y') . '.',
                ]
            );
        }
    }
}
