<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class DetailAlternatif extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "detail_alternatif";
    protected $fillable = [
        'id','alternatif_id','kriteria_id','nama_kriteria','subkriteria_id','nama_subkriteria'
    ];
}
