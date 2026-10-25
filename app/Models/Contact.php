<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Contact extends Model
{
    private const WARNA = [
        '#e4572e', '#17bebb', '#ffc914', '#76b041', '#6f42c1',
        '#d6336c', '#1c7ed6', '#f08c00', '#0ca678', '#7048e8',
    ];

    protected $fillable = [
        'category_id',
        'nama',
        'no_telepon',
        'email',
        'alamat',
        'tanggal_lahir',
    ];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Huruf pertama nama, dipakai untuk avatar bulat
    public function getInisialAttribute(): string
    {
        return mb_strtoupper(mb_substr(trim($this->nama), 0, 1)) ?: '?';
    }

    // Warna avatar, tetap sama untuk nama yang sama
    public function getWarnaAttribute(): string
    {
        return self::WARNA[crc32($this->nama) % count(self::WARNA)];
    }

    // Huruf pengelompokan daftar: A-Z, atau '#' kalau diawali angka/simbol
    public function getHurufAttribute(): string
    {
        return preg_match('/^[A-Z]$/', $this->inisial) ? $this->inisial : '#';
    }

    // Tanggal ulang tahun berikutnya (hari ini dihitung), null kalau tanggal lahir kosong
    public function ulangTahunBerikutnya(?Carbon $acuan = null): ?Carbon
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        $acuan = $acuan ?? today();
        $tanggal = $this->tanggalDiTahun($acuan->year);

        if ($tanggal->lt($acuan)) {
            $tanggal = $this->tanggalDiTahun($acuan->year + 1);
        }

        return $tanggal;
    }

    // Berapa hari lagi ulang tahun, hanya jika dalam batas hari (default 5). 0 = hari ini. Selain itu null.
    public function hariMenujuUlangTahun(int $batas = 5): ?int
    {
        $berikut = $this->ulangTahunBerikutnya();

        if (! $berikut) {
            return null;
        }

        $hari = (int) today()->diffInDays($berikut);

        return $hari <= $batas ? $hari : null;
    }

    private function tanggalDiTahun(int $tahun): Carbon
    {
        $lahir = $this->tanggal_lahir;
        $hari = $lahir->day;

        // Lahir 29 Februari: di tahun bukan kabisat dirayakan 28 Februari
        if ($lahir->month === 2 && $hari === 29 && ! Carbon::create($tahun, 1, 1)->isLeapYear()) {
            $hari = 28;
        }

        return Carbon::create($tahun, $lahir->month, $hari, 0, 0, 0);
    }
}
