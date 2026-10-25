<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama', 50);
            $table->timestamps();

            // Nama kategori tidak boleh kembar dalam satu akun
            $table->unique(['user_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
