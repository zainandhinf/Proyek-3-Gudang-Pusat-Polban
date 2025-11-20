<?php

use App\Http\Controllers\ProfileController;
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

    // ---------------------------------------------------------------------
    // Kategori Routes
    // ---------------------------------------------------------------------
    Route::get('/kategoris', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategoris/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategoris', [KategoriController::class, 'store'])->name('kategori.store');
    
    // Route Edit & Update (menggunakan Model Binding {kategori})
    Route::get('/kategoris/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategoris/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    
    // Route Destroy
    Route::delete('/kategoris/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});


require __DIR__ . '/auth.php';