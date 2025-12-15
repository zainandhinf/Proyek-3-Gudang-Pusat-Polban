<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangUsangController;
use App\Http\Controllers\LaporanController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\KelompokBarangController;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Kelompok Barang Routes
    Route::get('/kelompok-barang', [KelompokBarangController::class, 'index'])->name('kelompok-barang.index');
    Route::get('/kelompok-barang/create', [KelompokBarangController::class, 'create'])->name('kelompok-barang.create');
    Route::post('/kelompok-barang', [KelompokBarangController::class, 'store'])->name('kelompok-barang.store');
    
    Route::get('/kelompok-barang/{id}/edit', [KelompokBarangController::class, 'edit'])->name('kelompok-barang.edit');
    Route::put('/kelompok-barang/{id}', [KelompokBarangController::class, 'update'])->name('kelompok-barang.update');
    Route::delete('/kelompok-barang/{id}', [KelompokBarangController::class, 'destroy'])->name('kelompok-barang.destroy');

    // Satuan Routes
    Route::get('/satuans', [SatuanController::class, 'index'])->name('satuans.index');
    Route::get('/satuans/create', [SatuanController::class, 'create'])->name('satuans.create');
    Route::post('/satuans', [SatuanController::class, 'store'])->name('satuans.store');
    Route::get('/satuans/{satuan}/edit', [SatuanController::class, 'edit'])->name('satuans.edit');
    Route::put('/satuans/{satuan}', [SatuanController::class, 'update'])->name('satuans.update');
    Route::delete('/satuans/{satuan}', [SatuanController::class, 'destroy'])->name('satuans.destroy');

    // Barang Routes
    Route::get('/barangs', [BarangController::class, 'index'])->name('barangs.index');
    Route::get('/barangs/create', [BarangController::class, 'create'])->name('barangs.create');
    Route::post('/barangs', [BarangController::class, 'store'])->name('barangs.store');
    
    // Menggunakan {barang} agar cocok dengan Model Binding di Controller (public function edit(Barang $barang))
    Route::get('/barangs/{barang}/edit', [BarangController::class, 'edit'])->name('barangs.edit');
    Route::put('/barangs/{barang}', [BarangController::class, 'update'])->name('barangs.update');
    Route::delete('/barangs/{barang}', [BarangController::class, 'destroy'])->name('barangs.destroy');


    // Operator

    // // Barang Masuk Routes
    // Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    // Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    // Route::get('/barang-masuk/{id}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
    // Route::get('/barang-masuk/{id}/edit', [BarangMasukController::class, 'edit'])->name('barang-masuk.edit');
    // Route::put('/barang-masuk/{id}', [BarangMasukController::class, 'update'])->name('barang-masuk.update');
    // Route::delete('/barang-masuk/{id}', [BarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');
    
    // Mutasi Barang Routes
    Route::get('/mutasi-barang', [MutasiBarangController::class, 'index'])->name('mutasi-barang.index');
    Route::get('/mutasi-barang/create', [MutasiBarangController::class, 'create'])->name('mutasi-barang.create');
    Route::post('/mutasi-barang', [MutasiBarangController::class, 'store'])->name('mutasi-barang.store');
    Route::get('/mutasi-barang/{id}', [MutasiBarangController::class, 'show'])->name('mutasi-barang.show');
    Route::get('/mutasi-barang/{id}/edit', [MutasiBarangController::class, 'edit'])->name('mutasi-barang.edit');
    Route::put('/mutasi-barang/{id}', [MutasiBarangController::class, 'update'])->name('mutasi-barang.update');
    Route::delete('/mutasi-barang/{id}', [MutasiBarangController::class, 'destroy'])->name('mutasi-barang.destroy');

    // Permintaan Routes
    Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('/permintaan/{id}/proses', [PermintaanController::class, 'proses'])->name('permintaan.proses');
    Route::post('/permintaan/{id}/proses', [PermintaanController::class, 'prosesStore'])->name('permintaan.proses.store');
    Route::post('/permintaan/{id}/approve', [PermintaanController::class, 'approve'])->name('permintaan.approve');
    Route::post('/permintaan/{id}/reject', [PermintaanController::class, 'reject'])->name('permintaan.reject');
    Route::get('/permintaan/riwayat', [PermintaanController::class, 'riwayat'])->name('permintaan.riwayat');
    Route::get('/permintaan/detail/{id}', [PermintaanController::class, 'detail'])->name('permintaan.detail');

    // Stock Opname Routes
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');
    Route::get('/stock-opname/{id}/edit', [StockOpnameController::class, 'edit'])->name('stock-opname.edit');
    Route::put('/stock-opname/{id}', [StockOpnameController::class, 'update'])->name('stock-opname.update');
    Route::get('/stock-opname/{id}', [StockOpnameController::class, 'show'])->name('stock-opname.show');
    Route::delete('/stock-opname/{id}', [StockOpnameController::class, 'destroy'])->name('stock-opname.destroy');
    
    //Barang Usang Routes
    Route::get('/barang-usang', [BarangUsangController::class, 'index'])->name('barang-usang.index');
    Route::get('/barang-usang/create', [BarangUsangController::class, 'create'])->name('barang-usang.create');
    Route::post('/barang-usang', [BarangUsangController::class, 'store'])->name('barang-usang.store');
    Route::get('/barang-usang/{barangUsang}/edit', [BarangUsangController::class, 'edit'])->name('barang-usang.edit');
    Route::put('/barang-usang/{barangUsang}', [BarangUsangController::class, 'update'])->name('barang-usang.update');
    Route::delete('/barang-usang/{barangUsang}', [BarangUsangController::class, 'destroy'])->name('barang-usang.destroy');


    // Route::post('/detail-barang-masuk', [DetailBarangMasukController::class, 'store'])->name('detail-barang-masuk.store');
    
    
    // Pemohon
    Route::get('/permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');



    // Laporan Routes
    Route::get('/laporan/mutasi', [LaporanController::class, 'mutasi'])->name('laporan.mutasi');
    Route::get('/laporan/mutasi/export', [LaporanController::class, 'exportMutasi'])->name('laporan.mutasi.export');
    Route::get('/laporan/permintaan', [LaporanController::class, 'permintaan'])->name('laporan.permintaan');
    Route::get('/laporan/permintaan/export', [LaporanController::class, 'exportPermintaan'])->name('laporan.permintaan.export');
    Route::get('/laporan/stock-opname', [LaporanController::class, 'stockOpname'])->name('laporan.stock-opname');
    Route::get('/laporan/stock-opname/export', [LaporanController::class, 'exportStockOpname'])->name('laporan.stock-opname.export');
});


require __DIR__ . '/auth.php';