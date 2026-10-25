<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DaruratController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('contacts.index'));

// Hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Hanya untuk yang sudah login (route manual, tanpa Route::resource)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Kontak
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // Kategori (dipanggil lewat fetch dari form kontak)
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Riwayat / log notifikasi
    Route::get('/riwayat', [LogController::class, 'index'])->name('logs.index');
    Route::post('/riwayat/baca', [LogController::class, 'bacaSemua'])->name('logs.read');

    // Nomor darurat bawaan
    Route::get('/darurat', [DaruratController::class, 'index'])->name('darurat.index');

    // Backup & restore
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup/unduh', [BackupController::class, 'unduh'])->name('backup.download');
    Route::post('/backup/pulihkan', [BackupController::class, 'pulihkan'])->name('backup.restore');

    // Profil user
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});
