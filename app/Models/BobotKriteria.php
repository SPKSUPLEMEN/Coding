<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class BobotKriteria extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "bobot_kriteria";
    protected $fillable = [
        'id','kriteria1','kriteria2','bobot','eigen'
    ];
}
