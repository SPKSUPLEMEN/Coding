<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sub_kriteria');
        Schema::create('sub_kriteria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kriteria_id');
            $table->integer('kode');
            $table->string('nama', 100);
            $table->float('batas_bawah')->default(0);
            $table->float('batas_tengah')->default(5);
            $table->float('batas_atas')->default(10);
            $table->timestamps();

            $table->foreign('kriteria_id')
                  ->references('id')
                  ->on('kriteria')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kriteria');
    }
};
