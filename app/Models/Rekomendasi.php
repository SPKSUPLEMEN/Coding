<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Rekomendasi extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "rekomendasi";
    protected $fillable = [
        'id', 'user_id', 'alternatif_id', 'nama', 'skor', 'ranking'
    ];

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class, 'alternatif_id', 'id');
    }
}
