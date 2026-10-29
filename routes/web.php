<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenyakitController;
use App\Http\Controllers\GejalaController;
use App\Http\Controllers\RelasiController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\SolusiController;
use App\Http\Controllers\AturanController;
use App\Http\Controllers\SimulasiController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [DiagnosaController::class, 'index'])->name('home');
Route::get('/konsultasi', [DiagnosaController::class, 'konsultasi'])->name('konsultasi');
Route::post('/diagnosa/proses', [DiagnosaController::class, 'proses'])->name('diagnosa.proses');
Route::get('/diagnosa/hasil/{id}', [DiagnosaController::class, 'hasil'])->name('diagnosa.hasil');

// Dashboard
Route::get('/dashboard', function () {
    $gejala = \App\Models\Gejala::orderBy('kode_gejala', 'asc')->get();
    $penyakit = \App\Models\Penyakit::with(['aturans' => function ($q) {
        $q->where('is_active', true)->with('gejalas');
    }])->orderBy('kode_penyakit', 'asc')->get();
    $aturan = \App\Models\Aturan::with(['penyakit', 'gejalas'])->orderBy('kode_aturan', 'asc')->get();
    return view('dashboard', compact('gejala', 'penyakit', 'aturan'));
})->middleware(['auth'])->name('dashboard');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('penyakit', PenyakitController::class)->except(['show']);
    Route::resource('gejala', GejalaController::class)->except(['show']);
    Route::resource('aturan', AturanController::class)->except(['show']);
    Route::resource('relasi', RelasiController::class)->except(['show']);
    Route::resource('solusi', SolusiController::class)->except(['show', 'create', 'edit']);
    Route::get('hasil', [HasilController::class, 'index'])->name('hasil.index');
    Route::delete('hasil/destroy-all', [HasilController::class, 'destroyAll'])->name('hasil.destroy-all');
    Route::get('hasil/{hasil}', [HasilController::class, 'show'])->name('hasil.show');
    Route::delete('hasil/{hasil}', [HasilController::class, 'destroy'])->name('hasil.destroy');
    Route::get('simulasi', [SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('simulasi/proses', [SimulasiController::class, 'proses'])->name('simulasi.proses');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
