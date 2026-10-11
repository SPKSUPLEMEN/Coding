<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama sistem AHP yang sudah tidak digunakan
        // Urutan drop penting: tabel dengan FK dihapus dulu
        Schema::dropIfExists('kriteria_user');
        Schema::dropIfExists('detail_alternatif');
        Schema::dropIfExists('bobot_kriteria_mhs');
        Schema::dropIfExists('bobot_subkriteria');
        Schema::dropIfExists('bobot_kriteria');
    }

    public function down(): void
    {
        // Tidak perlu restore tabel lama
    }
};
