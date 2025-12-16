<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangUsangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KelompokBarangController;
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

// Dashboard pakai punya Teman (karena sudah ada grafiknya)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Pemohon
    Route::get('/manajemen-akun/pemohon', [PemohonController::class, 'index'])->name('manajemen-akun.pemohon.index');
    Route::get('/manajemen-akun/pemohon/create', [PemohonController::class, 'create'])->name('pemohon.create');
    Route::post('/manajemen-akun/pemohon', [PemohonController::class, 'store'])->name('pemohon.store');
    Route::delete('/manajemen-akun/pemohon/{id}', [PemohonController::class, 'destroy'])->name('pemohon.destroy');
    Route::get('/manajemen-akun/pemohon/{id}/edit', [PemohonController::class, 'edit'])->name('pemohon.edit');
    Route::put('/manajemen-akun/pemohon/{id}', [PemohonController::class, 'update'])->name('pemohon.update');

    // Approval
    Route::get('/manajemen-akun/approval', [ApprovalController::class, 'index'])->name('manajemen-akun.approval.index');
    Route::get('/manajemen-akun/approval/create', [ApprovalController::class, 'create'])->name('approval.create');
    Route::post('/manajemen-akun/approval', [ApprovalController::class, 'store'])->name('approval.store');
    Route::get('/manajemen-akun/approval/{id}/edit', [ApprovalController::class, 'edit'])->name('approval.edit');
    Route::put('/manajemen-akun/approval/{id}', [ApprovalController::class, 'update'])->name('approval.update');
    Route::delete('/manajemen-akun/approval/{id}', [ApprovalController::class, 'destroy'])->name('approval.destroy');


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
    Route::get('/barangs/{barang}/edit', [BarangController::class, 'edit'])->name('barangs.edit');
    Route::put('/barangs/{barang}', [BarangController::class, 'update'])->name('barangs.update');
    Route::delete('/barangs/{barang}', [BarangController::class, 'destroy'])->name('barangs.destroy');

    // Mutasi Barang Routes
    Route::get('/mutasi-barang', [MutasiBarangController::class, 'index'])->name('mutasi-barang.index');
    Route::get('/mutasi-barang/create', [MutasiBarangController::class, 'create'])->name('mutasi-barang.create');
    Route::post('/mutasi-barang', [MutasiBarangController::class, 'store'])->name('mutasi-barang.store');
    Route::get('/mutasi-barang/{id}', [MutasiBarangController::class, 'show'])->name('mutasi-barang.show');
    Route::get('/mutasi-barang/{id}/edit', [MutasiBarangController::class, 'edit'])->name('mutasi-barang.edit');
    Route::put('/mutasi-barang/{id}', [MutasiBarangController::class, 'update'])->name('mutasi-barang.update');
    Route::delete('/mutasi-barang/{id}', [MutasiBarangController::class, 'destroy'])->name('mutasi-barang.destroy');

    // Permintaan Routes (General & Approval)
    Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('/permintaan/{id}/proses', [PermintaanController::class, 'proses'])->name('permintaan.proses');
    Route::post('/permintaan/{id}/proses', [PermintaanController::class, 'prosesStore'])->name('permintaan.proses.store');
    Route::post('/permintaan/{id}/approve', [PermintaanController::class, 'approve'])->name('permintaan.approve');
    Route::post('/permintaan/{id}/reject', [PermintaanController::class, 'reject'])->name('permintaan.reject');
    Route::get('/permintaan/riwayat', [PermintaanController::class, 'riwayat'])->name('permintaan.riwayat');
    Route::get('/permintaan/detail/{id}', [PermintaanController::class, 'detail'])->name('permintaan.detail');

    // Permintaan Routes (Pemohon Create)
    Route::get('/permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');

    // Stock Opname Routes
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');
    Route::get('/stock-opname/{id}/edit', [StockOpnameController::class, 'edit'])->name('stock-opname.edit');
    Route::put('/stock-opname/{id}', [StockOpnameController::class, 'update'])->name('stock-opname.update');
    Route::get('/stock-opname/{id}', [StockOpnameController::class, 'show'])->name('stock-opname.show');
    Route::delete('/stock-opname/{id}', [StockOpnameController::class, 'destroy'])->name('stock-opname.destroy');
    
    // Barang Usang Routes
    Route::get('/barang-usang', [BarangUsangController::class, 'index'])->name('barang-usang.index');
    Route::get('/barang-usang/create', [BarangUsangController::class, 'create'])->name('barang-usang.create');
    Route::post('/barang-usang', [BarangUsangController::class, 'store'])->name('barang-usang.store');
    Route::get('/barang-usang/{barangUsang}/edit', [BarangUsangController::class, 'edit'])->name('barang-usang.edit');
    Route::put('/barang-usang/{barangUsang}', [BarangUsangController::class, 'update'])->name('barang-usang.update');
    Route::get('/barang-usang/{barangUsang}', [BarangUsangController::class, 'show'])->name('barang-usang.show');
    Route::delete('/barang-usang/{barangUsang}', [BarangUsangController::class, 'destroy'])->name('barang-usang.destroy');

    // Laporan Routes
    Route::get('/laporan/mutasi', [LaporanController::class, 'mutasi'])->name('laporan.mutasi');
    Route::get('/laporan/mutasi/export', [LaporanController::class, 'exportMutasi'])->name('laporan.mutasi.export');
    Route::post('/mutasi-barang/import', [MutasiBarangController::class, 'import'])->name('mutasi-barang.import');

    Route::get('/laporan/permintaan', [LaporanController::class, 'permintaan'])->name('laporan.permintaan');
    Route::get('/laporan/permintaan/export', [LaporanController::class, 'exportPermintaan'])->name('laporan.permintaan.export');

    Route::get('/laporan/stock-opname', [LaporanController::class, 'stockOpname'])->name('laporan.stock-opname');
    Route::get('/laporan/stock-opname/export', [LaporanController::class, 'exportStockOpname'])->name('laporan.stock-opname.export');
    Route::post('/stock-opname/import', [StockOpnameController::class, 'import'])->name('stock-opname.import');

    Route::get('/laporan/barang-usang', [LaporanController::class, 'barangUsang'])->name('laporan.barang-usang');
    Route::get('/laporan/barang-usang/export', [LaporanController::class, 'exportBarangUsang'])->name('laporan.barang-usang.export');
    Route::post('/barang-usang/import', [BarangUsangController::class, 'import'])->name('barang-usang.import');

    Route::get('/laporan/data-barang', [LaporanController::class, 'dataBarang'])->name('laporan.data-barang');
    Route::get('/laporan/data-barang/export', [LaporanController::class, 'exportDataBarang'])->name('laporan.data-barang.export');
    Route::post('/barangs/import', [BarangController::class, 'import'])->name('barangs.import');
    
    Route::get('/laporan/kartu-stok', [LaporanController::class, 'kartuStok'])->name('laporan.kartu-stok');
});

require __DIR__ . '/auth.php';