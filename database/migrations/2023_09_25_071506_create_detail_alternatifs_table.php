<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_alternatif', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('alternatif_id');
            $table->uuid('kriteria_id')->nullable();
            $table->string('nama_kriteria')->nullable();
            $table->uuid('subkriteria_id')->nullable();
            $table->string('nama_subkriteria')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_alternatifs');
    }
};
