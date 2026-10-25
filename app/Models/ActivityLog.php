<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'jenis',
        'pesan',
        'kunci',
        'dibaca_pada',
    ];

    protected function casts(): array
    {
        return ['dibaca_pada' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Cara singkat mencatat aktivitas: ActivityLog::catat($userId, 'kontak_ditambah', 'Kontak Adi ditambahkan.')
    public static function catat(int $userId, string $jenis, string $pesan): self
    {
        return self::create([
            'user_id' => $userId,
            'jenis' => $jenis,
            'pesan' => $pesan,
        ]);
    }

    // Kelas ikon Bootstrap Icons + warna sesuai jenis log
    public function getIkonAttribute(): string
    {
        return match ($this->jenis) {
            'kontak_ditambah' => 'bi-person-plus text-success',
            'kontak_diubah' => 'bi-pencil-square text-primary',
            'kontak_dihapus' => 'bi-person-dash text-danger',
            'ulang_tahun' => 'bi-cake2 text-warning',
            'backup' => 'bi-cloud-arrow-down text-info',
            'restore' => 'bi-cloud-arrow-up text-info',
            default => 'bi-bell text-secondary',
        };
    }
}
