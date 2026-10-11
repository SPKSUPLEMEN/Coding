<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Perankingan extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "perankingan";
    protected $fillable = [
        'id', 'alternatif_id', 'nilai'
        // nilai = hasil defuzzifikasi (0.00 - 1.00)
    ];

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class, 'alternatif_id', 'id');
    }
}
