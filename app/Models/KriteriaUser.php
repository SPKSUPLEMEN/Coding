<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class KriteriaUser extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "kriteria_user";
    protected $fillable = [
        'id','user_id','kriteria','subkriteria'
    ];
}
