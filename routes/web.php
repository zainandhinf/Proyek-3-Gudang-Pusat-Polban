<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DetailBarangMasukController;
use App\Http\Controllers\PermintaanController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Operator
    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    Route::get('/barang-masuk/{id}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
    Route::get('/barang-masuk/{id}/edit', [BarangMasukController::class, 'edit'])->name('barang-masuk.edit');
    Route::put('/barang-masuk/{id}', [BarangMasukController::class, 'update'])->name('barang-masuk.update');
    Route::delete('/barang-masuk/{id}', [BarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');
    
    Route::get('/mutasi-barang', [MutasiBarangController::class, 'index'])->name('mutasi-barang.index');
    Route::get('/mutasi-barang/create', [MutasiBarangController::class, 'create'])->name('mutasi-barang.create');
    Route::post('/mutasi-barang', [MutasiBarangController::class, 'store'])->name('mutasi-barang.store');
    Route::get('/mutasi-barang/{id}', [MutasiBarangController::class, 'show'])->name('mutasi-barang.show');
    Route::get('/mutasi-barang/{id}/edit', [MutasiBarangController::class, 'edit'])->name('mutasi-barang.edit');
    Route::put('/mutasi-barang/{id}', [MutasiBarangController::class, 'update'])->name('mutasi-barang.update');
    Route::delete('/mutasi-barang/{id}', [MutasiBarangController::class, 'destroy'])->name('mutasi-barang.destroy');

    Route::get('/permintaan/{id}/proses', [PermintaanController::class, 'proses'])->name('permintaan.proses');
    Route::post('/permintaan/{id}/proses', [PermintaanController::class, 'prosesStore'])->name('permintaan.proses.store');

    
    
    Route::post('/detail-barang-masuk', [DetailBarangMasukController::class, 'store'])->name('detail-barang-masuk.store');
    
    Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    
    // Pemohon
    Route::get('/permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
    
});

require __DIR__.'/auth.php';
