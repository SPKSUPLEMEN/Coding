<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\FuzzyRuleController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\AlternatifController;

// ── Auth routes ──────────────────────────────────────────────────────────────
Auth::routes();

// ── Profil & Home ────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/profil', [App\Http\Controllers\ProfilController::class, 'index'])->name('profil');
    Route::post('/ubah-profil', [App\Http\Controllers\ProfilController::class, 'ubahProfil'])->name('ubah-profil');
});

// ── Kriteria & SubKriteria ───────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ⚠️ PENTING: route STATIS harus di atas route DINAMIS ({id})
    // Jika /kriteria/datatable di bawah /kriteria/{id},
    // Laravel akan membaca "datatable" sebagai nilai {id} → error!

    Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::get('/kriteria/datatable', [KriteriaController::class, 'datatable'])->name('kriteria.datatable');   // ← statis, HARUS di atas {id}
    Route::post('/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::put('/kriteria/{id}', [KriteriaController::class, 'update'])->name('kriteria.update');              // ← dinamis, di bawah
    Route::delete('/kriteria/{id}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');

    // SubKriteria
    Route::get('/subkriteria', [SubKriteriaController::class, 'index'])->name('subkriteria.index');
    Route::post('/subkriteria/store', [KriteriaController::class, 'storeSubkriteria'])->name('subkriteria.store');
    Route::get('/subkriteria/datatable/{kriteriaId}', [KriteriaController::class, 'datatableSubkriteria'])->name('subkriteria.datatable'); // ← statis, di atas {id}
    Route::put('/subkriteria/{id}', [KriteriaController::class, 'updateSubkriteria'])->name('subkriteria.update');
    Route::delete('/subkriteria/{id}', [KriteriaController::class, 'destroySubkriteria'])->name('subkriteria.destroy');
    // Sub Kriteria — halaman mandiri

    Route::get('/subkriteria/datatableAll', [KriteriaController::class, 'datatableAll'])->name('subkriteria.datatableAll');
});

// ── Fuzzy Rules ──────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/fuzzy-rule', [FuzzyRuleController::class, 'index'])->name('fuzzy-rule.index');
    Route::get('/fuzzy-rule/datatable', [FuzzyRuleController::class, 'datatable'])->name('fuzzy-rule.datatable'); // ← statis, HARUS di atas {id}
    Route::get('/fuzzy-rule/{id}/edit', function ($id) {
        return response()->json(\App\Models\FuzzyRule::find($id));
    })->name('fuzzy-rule.edit');
    Route::post('/fuzzy-rule', [FuzzyRuleController::class, 'store'])->name('fuzzy-rule.store');
    Route::put('/fuzzy-rule/{id}', [FuzzyRuleController::class, 'update'])->name('fuzzy-rule.update');
    Route::delete('/fuzzy-rule/{id}', [FuzzyRuleController::class, 'destroy'])->name('fuzzy-rule.destroy');
});

// Seed (development only — hapus di production)
// Route::get('/fuzzy-rule/seed', [FuzzyRuleController::class, 'seedSampleData'])->name('fuzzy-rule.seed');

// ── Rekomendasi ──────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ⚠️ route statis /rekomendasi/create, /rekomendasi/hasil, dll
    // HARUS di atas Route::resource atau route dengan {id}

    Route::get('/rekomendasi/create', [RekomendasiController::class, 'create'])->name('rekomendasi.create');
    Route::get('/rekomendasi/hasil', [RekomendasiController::class, 'store'])->name('rekomendasi.store');
    Route::get('/rekomendasi/cetak', [RekomendasiController::class, 'cetak'])->name('rekomendasi.cetak');
    Route::post('/rekomendasi/cari', [RekomendasiController::class, 'cari'])->name('rekomendasi.cari');
    Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi.index');
    Route::get('/cetak', [RekomendasiController::class, 'cetak'])->name('cetak');
});

// ── Alternatif ───────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Hapus ini jika ada:
    // Route::resource('/alternatif', AlternatifController::class);

    // Ganti dengan:
    Route::get('/alternatif', [AlternatifController::class, 'index'])->name('alternatif.index');
    Route::get('/alternatif/datatable', [AlternatifController::class, 'datatable'])->name('alternatif.datatable');  // ← statis HARUS di atas {id}
    Route::get('/alternatif/{id}', [AlternatifController::class, 'show'])->name('alternatif.show');
    Route::post('/alternatif', [AlternatifController::class, 'store'])->name('alternatif.store');
    Route::put('/alternatif/{id}', [AlternatifController::class, 'update'])->name('alternatif.update');
    Route::delete('/alternatif/{id}', [AlternatifController::class, 'destroy'])->name('alternatif.destroy');
});

// DEBUG — hapus setelah selesai!
Route::get('/rekomendasi/debug', [RekomendasiController::class, 'debug'])->name('rekomendasi.debug');
