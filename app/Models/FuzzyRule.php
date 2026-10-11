<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class FuzzyRule extends Model
{
    use HasFactory;
    use Uuid;

    public $timestamps = true;
    protected $table = "fuzzy_rules";
    protected $fillable = [
        'id', 'nama_rule', 'kondisi', 'suplemen_id', 'bobot_rule'
    ];

    /**
     * kondisi disimpan sebagai JSON array of subkriteria_id
     * Contoh: ["uuid-subkriteria-1", "uuid-subkriteria-2", ...]
     */
    protected $casts = [
        'kondisi' => 'array',
    ];

    public function suplemen()
    {
        return $this->belongsTo(Alternatif::class, 'suplemen_id', 'id');
    }

    /**
     * Ambil detail kondisi lengkap dengan nama kriteria dan subkriteria
     */
    public function getKondisiDetailAttribute()
    {
        $detail = [];
        foreach ($this->kondisi as $subkriteriaId) {
            $sub = SubKriteria::with('kriteria')->find($subkriteriaId);
            if ($sub) {
                $detail[] = [
                    'kriteria'    => $sub->kriteria->nama,
                    'subkriteria' => $sub->nama,
                ];
            }
        }
        return $detail;
    }
}
