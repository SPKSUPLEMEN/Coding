<?php

// ============================================================
//  FILE: database/migrations/
// ============================================================
//  Jalankan migration sesuai urutan di bawah ini:
//
//  1. 2024_01_01_000001_create_kriteria_table.php
//  2. 2024_01_01_000002_create_sub_kriteria_table.php
//  3. 2024_01_01_000003_create_alternatif_table.php
//  4. 2024_01_01_000004_create_fuzzy_rules_table.php
//  5. 2024_01_01_000005_create_perankingan_table.php
//  6. 2024_01_01_000006_create_rekomendasi_table.php
//
//  Perintah: php artisan migrate --path=database/migrations
// ============================================================


// ─────────────────────────────────────────────────────────────
// 1. KRITERIA
//    Hapus kolom AHP (jumlah_bobot, jumlah_eigen, rata_eigen)
// ─────────────────────────────────────────────────────────────
// File: 2024_01_01_000001_create_kriteria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('kriteria');
        Schema::create('kriteria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('kode')->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};


// ─────────────────────────────────────────────────────────────
// 2. SUB KRITERIA (Himpunan Fuzzy)
//    Ganti kolom AHP dengan batas membership function segitiga
// ─────────────────────────────────────────────────────────────
// File: 2024_01_01_000002_create_sub_kriteria_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('sub_kriteria');
        Schema::create('sub_kriteria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kriteria_id');
            $table->integer('kode');
            $table->string('nama', 100);
            $table->float('batas_bawah')->default(0);   // a (titik kiri segitiga)
            $table->float('batas_tengah')->default(5);  // b (titik puncak)
            $table->float('batas_atas')->default(10);   // c (titik kanan)
            $table->timestamps();

            $table->foreign('kriteria_id')->references('id')->on('kriteria')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kriteria');
    }
};


// ─────────────────────────────────────────────────────────────
// 3. ALTERNATIF (Suplemen Gym)
//    Tambah kolom deskripsi
// ─────────────────────────────────────────────────────────────
// File: 2024_01_01_000003_create_alternatif_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('alternatif');
        Schema::create('alternatif', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternatif');
    }
};


// ─────────────────────────────────────────────────────────────
// 4. FUZZY RULES
//    Menggantikan tabel bobot_kriteria & bobot_subkriteria
// ─────────────────────────────────────────────────────────────
// File: 2024_01_01_000004_create_fuzzy_rules_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('fuzzy_rules');
        Schema::create('fuzzy_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_rule', 150);
            // kondisi: JSON array of sub_kriteria.id (operator AND)
            // Contoh: ["uuid1", "uuid2", "uuid3"]
            $table->json('kondisi');
            $table->uuid('suplemen_id');    // hasil THEN → alternatif
            $table->float('bobot_rule')->default(1.0); // 0.0 – 1.0
            $table->timestamps();

            $table->foreign('suplemen_id')->references('id')->on('alternatif')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuzzy_rules');
    }
};


// ─────────────────────────────────────────────────────────────
// 5. PERANKINGAN
//    Tidak berubah — nilai sekarang berisi hasil fuzzy (0–1)
// ─────────────────────────────────────────────────────────────
// File: 2024_01_01_000005_create_perankingan_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('perankingan');
        Schema::create('perankingan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('alternatif_id');
            $table->float('nilai')->default(0); // hasil defuzzifikasi (0–1)
            $table->timestamps();

            $table->foreign('alternatif_id')->references('id')->on('alternatif')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perankingan');
    }
};


// ─────────────────────────────────────────────────────────────
// 6. REKOMENDASI
//    Tambah kolom alternatif_id, skor, ranking
// ─────────────────────────────────────────────────────────────
// File: 2024_01_01_000006_create_rekomendasi_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('rekomendasi');
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('alternatif_id')->nullable();
            $table->string('nama', 150);
            $table->float('skor')->default(0);      // skor fuzzy (0–1)
            $table->integer('ranking')->default(0); // urutan rekomendasi
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('alternatif_id')->references('id')->on('alternatif')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};


// ─────────────────────────────────────────────────────────────
// TABEL YANG DIHAPUS (tidak diperlukan lagi):
//   - bobot_kriteria
//   - bobot_subkriteria
//   - bobot_kriteria_mhs
//   - detail_alternatif
//   - kriteria_user
// ─────────────────────────────────────────────────────────────
// File tambahan jika perlu drop tabel lama:

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('bobot_kriteria');
        Schema::dropIfExists('bobot_subkriteria');
        Schema::dropIfExists('bobot_kriteria_mhs');
        Schema::dropIfExists('detail_alternatif');
        Schema::dropIfExists('kriteria_user');
    }

    public function down(): void
    {
        // restore jika diperlukan
    }
};
