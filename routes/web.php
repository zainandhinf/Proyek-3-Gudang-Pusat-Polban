<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DetailBarangMasukController;
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

    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    Route::get('/barang-masuk/{id}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
    Route::get('/barang-masuk/{id}/edit', [BarangMasukController::class, 'edit'])->name('barang-masuk.edit');
    Route::put('/barang-masuk/{id}', [BarangMasukController::class, 'update'])->name('barang-masuk.update');
    Route::delete('/barang-masuk/{id}', [BarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');

    Route::post('/detail-barang-masuk', [DetailBarangMasukController::class, 'store'])->name('detail-barang-masuk.store');

});

require __DIR__.'/auth.php';
