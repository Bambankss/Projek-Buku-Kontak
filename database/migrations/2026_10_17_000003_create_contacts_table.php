<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('no_telepon', 20); // string, bukan integer (nol di depan hilang)
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();

            // Email kontak tidak boleh kembar dalam satu akun
            $table->unique(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
