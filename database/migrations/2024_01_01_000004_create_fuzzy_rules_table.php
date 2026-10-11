<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('fuzzy_rules');
        Schema::create('fuzzy_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_rule', 150);
            $table->json('kondisi');
            $table->uuid('suplemen_id');
            $table->float('bobot_rule')->default(1.0);
            $table->timestamps();

            $table->foreign('suplemen_id')
                  ->references('id')
                  ->on('alternatif')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuzzy_rules');
    }
};
