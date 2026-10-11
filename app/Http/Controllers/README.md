# 📦 Konversi AHP → Fuzzy Mamdani
## Sistem Rekomendasi Suplemen Gym — Laravel

---

## 📁 Struktur File yang Diubah

```
app/
├── Http/Controllers/
│   ├── KriteriaController.php      ✏️  DIUBAH  (hapus AHP, tambah CRUD sub fuzzy)
│   ├── RekomendasiController.php   ✏️  DIUBAH  (ganti logika AHP → Fuzzy Mamdani)
│   └── FuzzyRuleController.php     🆕  BARU    (kelola IF-THEN rules)
│
└── Models/
    ├── Kriteria.php                ✏️  DIUBAH  (hapus kolom AHP)
    ├── SubKriteria.php             ✏️  DIUBAH  (tambah batas_bawah/tengah/atas)
    ├── Alternatif.php              ✏️  DIUBAH  (tambah deskripsi, relasi)
    ├── FuzzyRule.php               🆕  BARU    (menggantikan BobotKriteria)
    ├── Perankingan.php             ✅  SAMA    (nilai sekarang = skor fuzzy 0–1)
    └── Rekomendasi.php             ✏️  DIUBAH  (tambah kolom skor, ranking)

database/migrations/
    └── migrations_all.php          (referensi semua migration)
```

---

## 🗑️ File yang Dihapus / Tidak Digunakan

| File | Alasan |
|------|--------|
| `BobotKriteria.php` | Diganti `FuzzyRule.php` |
| `BobotSubkriteria.php` | Tidak relevan di Fuzzy |
| `BobotKriteriaMhs.php` | Tidak relevan di Fuzzy |
| `DetailAlternatif.php` | Diganti kondisi JSON di `FuzzyRule` |
| `KriteriaUser.php` | Diganti kolom di `rekomendasi` |

---

## 🔄 Alur Sistem Fuzzy Mamdani

```
User Input (nilai 0–10 per kriteria)
        │
        ▼
┌─────────────────────┐
│   FUZZIFIKASI        │  Konversi nilai crisp → derajat keanggotaan (0–1)
│   Fungsi Segitiga    │  μ(x) = trimf(x, bawah, tengah, atas)
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│   INFERENSI          │  Evaluasi setiap Fuzzy Rule
│   Operator AND=MIN   │  fire = MIN(μ1, μ2, ...) × bobot_rule
│   Agregasi MAX       │  per suplemen → ambil MAX fire
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│   DEFUZZIFIKASI      │  Normalisasi skor → ranking suplemen
│   Weighted Average   │  skor = fire / max_fire
└─────────┬───────────┘
          │
          ▼
    Hasil Rekomendasi
    (ranking + skor 0–1)
```

---

## ⚙️ Setup & Instalasi

### 1. Jalankan Migration
Buat 6 file migration terpisah dari `migrations_all.php`, lalu:
```bash
php artisan migrate:fresh
```

### 2. Tambahkan Route
```php
// routes/web.php

// Rekomendasi
Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi.index');
Route::get('/rekomendasi/create', [RekomendasiController::class, 'create'])->name('rekomendasi.create');
Route::post('/rekomendasi/cari', [RekomendasiController::class, 'cari'])->name('rekomendasi.cari');
Route::get('/rekomendasi/hasil', [RekomendasiController::class, 'store'])->name('rekomendasi.store');
Route::get('/rekomendasi/cetak', [RekomendasiController::class, 'cetak'])->name('rekomendasi.cetak');

// Kriteria & SubKriteria
Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
Route::post('/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
Route::put('/kriteria/{id}', [KriteriaController::class, 'update'])->name('kriteria.update');
Route::delete('/kriteria/{id}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');
Route::get('/kriteria/datatable', [KriteriaController::class, 'datatable'])->name('kriteria.datatable');
// SubKriteria
Route::post('/subkriteria', [KriteriaController::class, 'storeSubkriteria'])->name('subkriteria.store');
Route::put('/subkriteria/{id}', [KriteriaController::class, 'updateSubkriteria'])->name('subkriteria.update');
Route::delete('/subkriteria/{id}', [KriteriaController::class, 'destroySubkriteria'])->name('subkriteria.destroy');
Route::get('/subkriteria/datatable/{kriteriaId}', [KriteriaController::class, 'datatableSubkriteria'])->name('subkriteria.datatable');

// Fuzzy Rules
Route::get('/fuzzy-rule', [FuzzyRuleController::class, 'index'])->name('fuzzy-rule.index');
Route::post('/fuzzy-rule', [FuzzyRuleController::class, 'store'])->name('fuzzy-rule.store');
Route::put('/fuzzy-rule/{id}', [FuzzyRuleController::class, 'update'])->name('fuzzy-rule.update');
Route::delete('/fuzzy-rule/{id}', [FuzzyRuleController::class, 'destroy'])->name('fuzzy-rule.destroy');
Route::get('/fuzzy-rule/datatable', [FuzzyRuleController::class, 'datatable'])->name('fuzzy-rule.datatable');

// Seed sample data (gunakan hanya saat development)
Route::get('/fuzzy-rule/seed', [FuzzyRuleController::class, 'seedSampleData'])->name('fuzzy-rule.seed');
```

### 3. Daftarkan Model di `config/app.php` (jika belum auto-discover)
Tidak perlu jika menggunakan Laravel 8+.

### 4. Seed Sample Data
Akses URL berikut sekali di browser untuk mengisi data awal:
```
http://localhost/fuzzy-rule/seed
```
Ini akan mengisi:
- 5 Kriteria + 15 SubKriteria (himpunan fuzzy)
- 5 Suplemen gym
- 15 Fuzzy Rules

---

## 📊 Contoh Data Membership Function

| Kriteria | Himpunan | Bawah | Tengah | Atas |
|----------|----------|-------|--------|------|
| Harga | Murah | 0 | 1 | 4 |
| Harga | Sedang | 3 | 5 | 7 |
| Harga | Mahal | 6 | 9 | 10 |
| Protein | Sedang | 0 | 2 | 5 |
| Protein | Tinggi | 4 | 6 | 8 |
| Protein | Sangat Tinggi | 7 | 9 | 10 |

---

## 📋 Contoh Fuzzy Rules

| Rule | IF Harga | IF Isi | IF Protein | IF Kand. Gizi | IF Kualitas | THEN |
|------|----------|--------|------------|---------------|-------------|------|
| R1 | Murah | Banyak | Tinggi | Sedang | Agak Baik | Whey Protein |
| R5 | Sedang | Sedang | Sedang | Banyak | Baik | BCAA |
| R7 | Mahal | Sedikit | Tinggi | Banyak | Sangat Baik | Creatine |
| R10 | Murah | Banyak | Sangat Tinggi | Banyak | Agak Baik | Mass Gainer |
| R13 | Sedang | Sedang | Sedang | Sangat Banyak | Baik | Multivitamin |

---

## 🧮 Contoh Perhitungan Manual

**Input user:** Harga=3, Isi=6, Protein=7, Kandungan Gizi=8, Kualitas=7

**Fuzzifikasi:**
- Harga=3 → μMurah=0.00, μSedang=0.00, μMahal=0.00  *(tepat di batas)*
- Protein=7 → μSedang=0.00, μTinggi=0.50, μSangatTinggi=0.00
- Kualitas=7 → μAgakBaik=0.00, μBaik=0.50, μSangatBaik=0.00

**Inferensi Rule 2** (IF Harga=Sedang AND Protein=Tinggi AND ... THEN Whey Protein):
- fire = MIN(μSedang_harga, μTinggi_protein, ...) × 1.0

**Defuzzifikasi:**
- Skor tiap suplemen dinormalisasi → suplemen dengan skor tertinggi = rekomendasi utama

---

## ❓ FAQ

**Q: Kenapa pakai Fuzzy Mamdani bukan Sugeno?**
A: Mamdani lebih intuitif untuk domain rekomendasi berbasis kategori (Murah/Sedang/Mahal).
   Sugeno lebih cocok untuk output numerik kontinu.

**Q: Apakah bisa tanpa login?**
A: Bisa, ubah `Auth::id()` di `RekomendasiController` menjadi session ID atau value statis.

**Q: Bagaimana menambah suplemen baru?**
A: Tambah via tabel `alternatif`, lalu buat Fuzzy Rule baru melalui `/fuzzy-rule`.
