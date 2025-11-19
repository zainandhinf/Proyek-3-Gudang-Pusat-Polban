<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BarangController;
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
    
    // Pastikan baris ini aktif (tidak ada // di depannya) dan parameternya {kategori}
    Route::get('/kategoris/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategoris/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategoris/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('/kategoris/{id}', [KategoriController::class, 'show'])->name('kategori.show');

    // Satuan Routes
    Route::get('/satuans', [SatuanController::class, 'index'])->name('satuans.index');
    Route::get('/satuans/create', [SatuanController::class, 'create'])->name('satuans.create');
    Route::post('/satuans', [SatuanController::class, 'store'])->name('satuans.store');
    Route::get('/satuans/{satuan}/edit', [SatuanController::class, 'edit'])->name('satuans.edit');
    Route::put('/satuans/{satuan}', [SatuanController::class, 'update'])->name('satuans.update');
    Route::delete('/satuans/{satuan}', [SatuanController::class, 'destroy'])->name('satuans.destroy');

// --- Barang Routes ---
    Route::get('/barangs', [BarangController::class, 'index'])->name('barangs.index');
    Route::get('/barangs/create', [BarangController::class, 'create'])->name('barangs.create');
    Route::post('/barangs', [BarangController::class, 'store'])->name('barangs.store');
    
    // Menggunakan {barang} agar cocok dengan Model Binding di Controller (public function edit(Barang $barang))
    Route::get('/barangs/{barang}/edit', [BarangController::class, 'edit'])->name('barangs.edit');
    Route::put('/barangs/{barang}', [BarangController::class, 'update'])->name('barangs.update');
    Route::delete('/barangs/{barang}', [BarangController::class, 'destroy'])->name('barangs.destroy');

});


require __DIR__ . '/auth.php';