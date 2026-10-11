<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Alternatif extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "alternatif";
    protected $fillable = [
        'id', 'nama', 'deskripsi'
    ];

    public function fuzzyRules()
    {
        return $this->hasMany(FuzzyRule::class, 'suplemen_id', 'id');
    }

    public function perankingan()
    {
        return $this->hasOne(Perankingan::class, 'alternatif_id', 'id');
    }
}
