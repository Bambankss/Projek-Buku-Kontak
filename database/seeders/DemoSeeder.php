<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// Data contoh untuk demo. Jalankan: php artisan db:seed --class=DemoSeeder
// Login: demo@example.com / password123
// Tanggal lahir beberapa kontak diatur relatif terhadap HARI INI, jadi pengingat ulang tahun langsung muncul.
// Jalankan lagi seeder-nya kapan saja untuk menyegarkan tanggal itu.
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => 'password123',
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            ]
        );

        $kategori = [];
        foreach (['Keluarga', 'Teman', 'Kerja', 'Kampus'] as $nama) {
            $kategori[$nama] = $user->categories()->firstOrCreate(['nama' => $nama]);
        }

        // [nama, kategori, telepon, ulang tahun berapa hari lagi (null = tanpa tanggal lahir)]
        $kontak = [
            ['Ayah', 'Keluarga', '081111111111', null],
            ['Ibu', 'Keluarga', '081111111112', 0],
            ['Adi Nugroho', 'Teman', '082222222221', 3],
            ['Andini Putri', 'Kampus', '082222222222', null],
            ['Budi Santoso', 'Kerja', '083333333331', 5],
            ['Citra Lestari', 'Teman', '083333333332', 20],
            ['Dosen Pembimbing', 'Kampus', '084444444441', null],
            ['Eko Prasetyo', 'Kerja', '084444444442', 90],
            ['farhan Maulana', 'Teman', '085555555551', 1],
            ['Gita Anjani', 'Kampus', '085555555552', null],
            ['Hendra Wijaya', 'Kerja', '086666666661', null],
            ['Kakak', 'Keluarga', '086666666662', null],
            ['Tante Rina', 'Keluarga', '087777777771', 8],
            ['Zahra Aulia', 'Teman', '087777777772', null],
            ['7-Eleven Kampus', 'Kampus', '088888888881', null],
        ];

        foreach ($kontak as $i => [$nama, $kat, $telp, $hariLagi]) {
            $lahir = $hariLagi === null
                ? null
                : now()->addDays($hariLagi)->subYears(20 + $i)->format('Y-m-d');

            $user->contacts()->updateOrCreate(
                ['nama' => $nama],
                [
                    'category_id' => $kategori[$kat]->id,
                    'no_telepon' => $telp,
                    'alamat' => 'Jl. Contoh No. ' . ($i + 1) . ', Jakarta',
                    'tanggal_lahir' => $lahir,
                ]
            );
        }
    }
}
