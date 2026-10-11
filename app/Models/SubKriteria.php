<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class SubKriteria extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "sub_kriteria";
    protected $fillable = [
        'id', 'kriteria_id', 'kode', 'nama',
        'batas_bawah', 'batas_tengah', 'batas_atas'
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'id');
    }
}
