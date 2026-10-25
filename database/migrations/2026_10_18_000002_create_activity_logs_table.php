<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jenis', 30);      // kontak_ditambah, kontak_diubah, kontak_dihapus, ulang_tahun, backup, restore
            $table->string('pesan');
            $table->string('kunci')->nullable(); // supaya pengingat ulang tahun hanya dicatat sekali per tahun
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'kunci']);
            $table->index(['user_id', 'dibaca_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
