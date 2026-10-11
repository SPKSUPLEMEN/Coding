<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('rekomendasi');
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            // PENTING: nullable() harus ada sebelum foreign key dengan onDelete('set null')
            // Tipe harus sama persis dengan alternatif.id (uuid)
            $table->uuid('alternatif_id')->nullable();
            $table->string('nama', 150);
            $table->float('skor')->default(0);
            $table->integer('ranking')->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('alternatif_id')
                  ->references('id')
                  ->on('alternatif')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
