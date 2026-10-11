<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class BobotSubkriteria extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "bobot_subkriteria";
    protected $fillable = [
        'id','kriteria_id','subkriteria_id','kriteria1','kriteria2','bobot','eigen'
    ];
}
