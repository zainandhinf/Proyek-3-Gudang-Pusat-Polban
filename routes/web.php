<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SatuanController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\KategoriController;



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
    
    // Kategori Routes
    Route::get('/kategoris', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategoris/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategoris', [KategoriController::class, 'store'])->name('kategori.store');
    // Route::get('/kategoris/{id}', [KategoriController::class, 'show'])->name('kategori.show');
    // Route::get('/kategoris/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategoris/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    // Route::delete('/kategoris/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // Satuan Routes
    Route::get('/satuans', [SatuanController::class, 'index'])->name('satuans.index');
    Route::get('/satuans/create', [SatuanController::class, 'create'])->name('satuans.create');
    Route::post('/satuans', [SatuanController::class, 'store'])->name('satuans.store');
    Route::get('/satuans/{satuan}/edit', [SatuanController::class, 'edit'])->name('satuans.edit');
    Route::put('/satuans/{satuan}', [SatuanController::class, 'update'])->name('satuans.update');
    Route::delete('/satuans/{satuan}', [SatuanController::class, 'destroy'])->name('satuans.destroy');

});


require __DIR__ . '/auth.php';