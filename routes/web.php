<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\ApprovalController;
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
    Route::get('/manajemen-akun/pemohon', [PemohonController::class, 'index'])->name('manajemen-akun.pemohon.index');
    Route::get('/manajemen-akun/pemohon/create', [PemohonController::class, 'create'])->name('pemohon.create');
    Route::post('/manajemen-akun/pemohon', [PemohonController::class, 'store'])->name('pemohon.store');
    Route::delete('/manajemen-akun/pemohon/{id}', [PemohonController::class, 'destroy'])->name('pemohon.destroy');
    Route::get('/manajemen-akun/pemohon/{id}/edit', [PemohonController::class, 'edit'])->name('pemohon.edit');
    Route::put('/manajemen-akun/pemohon/{id}', [PemohonController::class, 'update'])->name('pemohon.update');

    Route::get('/manajemen-akun/approval', [ApprovalController::class, 'index'])->name('manajemen-akun.approval.index');
    Route::get('/manajemen-akun/approval/create', [ApprovalController::class, 'create'])->name('approval.create');
    Route::post('/manajemen-akun/approval', [ApprovalController::class, 'store'])->name('approval.store');
    Route::get('/manajemen-akun/approval/{id}/edit', [ApprovalController::class, 'edit'])->name('approval.edit');
    Route::put('/manajemen-akun/approval/{id}', [ApprovalController::class, 'update'])->name('approval.update');
    Route::delete('/manajemen-akun/approval/{id}', [ApprovalController::class, 'destroy'])->name('approval.destroy');
});

require __DIR__.'/auth.php';
