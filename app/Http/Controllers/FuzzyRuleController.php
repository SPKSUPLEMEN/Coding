<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyRule;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use DB, DataTables, Validator;

class FuzzyRuleController extends Controller
{

    public function index()
    {
        $suplemen  = Alternatif::orderBy('nama')->get(['id', 'nama']);
        $kriteria  = Kriteria::with('subKriteria')->orderBy('kode')->get();

        return view('pages.fuzzy-rule.index', compact('suplemen', 'kriteria'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama_rule'   => 'required|string|max:150',
                'suplemen_id' => 'required|exists:alternatif,id',
                'kondisi'     => 'required|array|min:1',
                'kondisi.*'   => 'exists:sub_kriteria,id',
                'bobot_rule'  => 'nullable|numeric|min:0|max:1',
            ], [
                'nama_rule.required'   => 'Nama rule tidak boleh kosong.',
                'suplemen_id.required' => 'Suplemen (THEN) wajib dipilih.',
                'kondisi.required'     => 'Minimal 1 kondisi (IF) harus dipilih.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation error', 'message' => $validator->messages()], 400);
            }

            // Validasi: setiap kriteria hanya boleh muncul 1x dalam 1 rule
            $kriteriaIds = SubKriteria::whereIn('id', $request->kondisi)->pluck('kriteria_id')->toArray();
            if (count($kriteriaIds) !== count(array_unique($kriteriaIds))) {
                return response()->json([
                    'status'  => 'validation error',
                    'message' => 'Setiap kriteria hanya boleh dipilih satu himpunan fuzzy per rule.',
                ], 400);
            }

            FuzzyRule::create([
                'nama_rule'   => $request->nama_rule,
                'suplemen_id' => $request->suplemen_id,
                'kondisi'     => $request->kondisi,
                'bobot_rule'  => $request->bobot_rule ?? 1.0,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Fuzzy rule berhasil disimpan.'], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama_rule'   => 'required|string|max:150',
                'suplemen_id' => 'required|exists:alternatif,id',
                'kondisi'     => 'required|array|min:1',
                'kondisi.*'   => 'exists:sub_kriteria,id',
                'bobot_rule'  => 'nullable|numeric|min:0|max:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation error', 'message' => $validator->messages()], 400);
            }

            $kriteriaIds = SubKriteria::whereIn('id', $request->kondisi)->pluck('kriteria_id')->toArray();
            if (count($kriteriaIds) !== count(array_unique($kriteriaIds))) {
                return response()->json([
                    'status'  => 'validation error',
                    'message' => 'Setiap kriteria hanya boleh dipilih satu himpunan fuzzy per rule.',
                ], 400);
            }

            FuzzyRule::where('id', $id)->update([
                'nama_rule'   => $request->nama_rule,
                'suplemen_id' => $request->suplemen_id,
                'kondisi'     => $request->kondisi,
                'bobot_rule'  => $request->bobot_rule ?? 1.0,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Fuzzy rule berhasil diperbarui.'], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            FuzzyRule::find($id)->delete();
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Fuzzy rule berhasil dihapus.'], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function datatable()
    {
        $rules = FuzzyRule::with('suplemen')->orderBy('created_at', 'ASC')->get();

        return DataTables::of($rules)
            ->addIndexColumn()
            ->addColumn('nama_rule', fn($d) => $d->nama_rule)
            ->addColumn('kondisi_detail', function ($d) {
                $labels = collect($d->kondisi_detail)
                    ->map(fn($c) => '<span class="badge badge-info text-dark">' . $c['kriteria'] . ' = ' . $c['subkriteria'] . '</span>')
                    ->implode(' ');
                return $labels;
            })
            ->addColumn('suplemen', fn($d) => $d->suplemen ? $d->suplemen->nama : '-')
            ->addColumn('bobot_rule', fn($d) => $d->bobot_rule)
            ->addColumn('action', function ($d) {
                return '
                    <a href="javascript:void(0)" data-id="' . $d->id . '"
                        class="edit btn btn-primary btn-sm show-edit-modal"><i class="fas fa-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $d->id . '"
                        class="btn btn-danger btn-sm show-delete-modal"><i class="fas fa-trash-alt"></i></a>
                ';
            })
            ->rawColumns(['kondisi_detail', 'action'])
            ->make(true);
    }

    /**
     * Seed sample data: 5 suplemen + 15 rules sesuai domain gym
     */
    public function seedSampleData()
    {
        // ── STEP 1: Truncate di luar transaction ────────────────────────────
        // TRUNCATE di MySQL otomatis commit transaction aktif,
        // sehingga HARUS dijalankan sebelum beginTransaction()
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            FuzzyRule::truncate();
            \App\Models\Perankingan::truncate();
            \App\Models\Rekomendasi::truncate();
            Alternatif::truncate();
            \App\Models\SubKriteria::truncate();
            \App\Models\Kriteria::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $th) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return response()->json(['status' => 'failed', 'message' => 'Gagal truncate: ' . $th->getMessage()], 500);
        }

        // ── STEP 2: Insert data baru dalam transaction ──────────────────────
        DB::beginTransaction();

        try {
            // ── Kriteria & SubKriteria (Himpunan Fuzzy) ─────────────────────
            $kriteriaData = [
                [
                    'nama' => 'Harga',
                    'sub'  => [
                        ['nama' => 'Murah',  'bawah' => 0, 'tengah' => 1, 'atas' => 4],
                        ['nama' => 'Sedang', 'bawah' => 3, 'tengah' => 5, 'atas' => 7],
                        ['nama' => 'Mahal',  'bawah' => 6, 'tengah' => 9, 'atas' => 10],
                    ],
                ],
                [
                    'nama' => 'Isi',
                    'sub'  => [
                        ['nama' => 'Sedikit', 'bawah' => 0, 'tengah' => 1, 'atas' => 4],
                        ['nama' => 'Sedang',  'bawah' => 3, 'tengah' => 5, 'atas' => 7],
                        ['nama' => 'Banyak',  'bawah' => 6, 'tengah' => 9, 'atas' => 10],
                    ],
                ],
                [
                    'nama' => 'Protein',
                    'sub'  => [
                        ['nama' => 'Sedang',        'bawah' => 0, 'tengah' => 2, 'atas' => 5],
                        ['nama' => 'Tinggi',        'bawah' => 4, 'tengah' => 6, 'atas' => 8],
                        ['nama' => 'Sangat Tinggi', 'bawah' => 7, 'tengah' => 9, 'atas' => 10],
                    ],
                ],
                [
                    'nama' => 'Kandungan Gizi',
                    'sub'  => [
                        ['nama' => 'Sedang',        'bawah' => 0, 'tengah' => 2, 'atas' => 5],
                        ['nama' => 'Banyak',        'bawah' => 4, 'tengah' => 6, 'atas' => 8],
                        ['nama' => 'Sangat Banyak', 'bawah' => 7, 'tengah' => 9, 'atas' => 10],
                    ],
                ],
                [
                    'nama' => 'Kualitas',
                    'sub'  => [
                        ['nama' => 'Agak Baik',   'bawah' => 0, 'tengah' => 2, 'atas' => 5],
                        ['nama' => 'Baik',        'bawah' => 4, 'tengah' => 6, 'atas' => 8],
                        ['nama' => 'Sangat Baik', 'bawah' => 7, 'tengah' => 9, 'atas' => 10],
                    ],
                ],
            ];

            $kriteriaMap    = [];
            $subkriteriaMap = [];

            foreach ($kriteriaData as $idx => $k) {
                $kriteria = \App\Models\Kriteria::create([
                    'kode' => $idx + 1,
                    'nama' => $k['nama'],
                ]);
                $kriteriaMap[$k['nama']] = $kriteria;

                foreach ($k['sub'] as $sidx => $s) {
                    $sub = \App\Models\SubKriteria::create([
                        'kriteria_id'  => $kriteria->id,
                        'kode'         => $sidx + 1,
                        'nama'         => $s['nama'],
                        'batas_bawah'  => $s['bawah'],
                        'batas_tengah' => $s['tengah'],
                        'batas_atas'   => $s['atas'],
                    ]);
                    $subkriteriaMap[$k['nama'] . '|' . $s['nama']] = $sub;
                }
            }

            // ── Suplemen (Alternatif) ────────────────────────────────────────
            $suplemenData = [
                ['nama' => 'Whey Protein',    'deskripsi' => 'Suplemen protein cepat serap, cocok untuk recovery otot pasca latihan.'],
                ['nama' => 'BCAA',            'deskripsi' => 'Branched Chain Amino Acid, menjaga massa otot dan mengurangi kelelahan.'],
                ['nama' => 'Creatine',        'deskripsi' => 'Meningkatkan kekuatan dan performa latihan intensitas tinggi.'],
                ['nama' => 'Mass Gainer',     'deskripsi' => 'Tinggi kalori dan protein, ideal untuk program bulking.'],
                ['nama' => 'Multivitamin Gym','deskripsi' => 'Memenuhi kebutuhan vitamin & mineral harian para atlet gym.'],
            ];

            $suplemenMap = [];
            foreach ($suplemenData as $s) {
                $alt = Alternatif::create($s);
                $suplemenMap[$s['nama']] = $alt;
            }

            // ── Fuzzy Rules ──────────────────────────────────────────────────
            $rulesData = [
                ['nama' => 'Rule 1',  'suplemen' => 'Whey Protein',    'kondisi' => ['Harga|Murah',  'Isi|Banyak',  'Protein|Tinggi',        'Kandungan Gizi|Sedang',       'Kualitas|Agak Baik']],
                ['nama' => 'Rule 2',  'suplemen' => 'Whey Protein',    'kondisi' => ['Harga|Sedang', 'Isi|Sedang',  'Protein|Tinggi',        'Kandungan Gizi|Banyak',       'Kualitas|Baik']],
                ['nama' => 'Rule 3',  'suplemen' => 'Whey Protein',    'kondisi' => ['Harga|Mahal',  'Isi|Sedang',  'Protein|Tinggi',        'Kandungan Gizi|Sangat Banyak','Kualitas|Sangat Baik']],
                ['nama' => 'Rule 4',  'suplemen' => 'BCAA',            'kondisi' => ['Harga|Murah',  'Isi|Sedikit', 'Protein|Sedang',        'Kandungan Gizi|Sedang',       'Kualitas|Agak Baik']],
                ['nama' => 'Rule 5',  'suplemen' => 'BCAA',            'kondisi' => ['Harga|Sedang', 'Isi|Sedang',  'Protein|Sedang',        'Kandungan Gizi|Banyak',       'Kualitas|Baik']],
                ['nama' => 'Rule 6',  'suplemen' => 'BCAA',            'kondisi' => ['Harga|Mahal',  'Isi|Banyak',  'Protein|Tinggi',        'Kandungan Gizi|Sangat Banyak','Kualitas|Sangat Baik']],
                ['nama' => 'Rule 7',  'suplemen' => 'Creatine',        'kondisi' => ['Harga|Mahal',  'Isi|Sedikit', 'Protein|Tinggi',        'Kandungan Gizi|Banyak',       'Kualitas|Sangat Baik']],
                ['nama' => 'Rule 8',  'suplemen' => 'Creatine',        'kondisi' => ['Harga|Sedang', 'Isi|Sedikit', 'Protein|Tinggi',        'Kandungan Gizi|Banyak',       'Kualitas|Sangat Baik']],
                ['nama' => 'Rule 9',  'suplemen' => 'Creatine',        'kondisi' => ['Harga|Mahal',  'Isi|Sedang',  'Protein|Sangat Tinggi', 'Kandungan Gizi|Sangat Banyak','Kualitas|Sangat Baik']],
                ['nama' => 'Rule 10', 'suplemen' => 'Mass Gainer',     'kondisi' => ['Harga|Murah',  'Isi|Banyak',  'Protein|Sangat Tinggi', 'Kandungan Gizi|Banyak',       'Kualitas|Agak Baik']],
                ['nama' => 'Rule 11', 'suplemen' => 'Mass Gainer',     'kondisi' => ['Harga|Sedang', 'Isi|Banyak',  'Protein|Sangat Tinggi', 'Kandungan Gizi|Sangat Banyak','Kualitas|Baik']],
                ['nama' => 'Rule 12', 'suplemen' => 'Mass Gainer',     'kondisi' => ['Harga|Murah',  'Isi|Sedang',  'Protein|Sangat Tinggi', 'Kandungan Gizi|Sedang',       'Kualitas|Agak Baik']],
                ['nama' => 'Rule 13', 'suplemen' => 'Multivitamin Gym','kondisi' => ['Harga|Sedang', 'Isi|Sedang',  'Protein|Sedang',        'Kandungan Gizi|Sangat Banyak','Kualitas|Baik']],
                ['nama' => 'Rule 14', 'suplemen' => 'Multivitamin Gym','kondisi' => ['Harga|Murah',  'Isi|Banyak',  'Protein|Sedang',        'Kandungan Gizi|Banyak',       'Kualitas|Agak Baik']],
                ['nama' => 'Rule 15', 'suplemen' => 'Multivitamin Gym','kondisi' => ['Harga|Sedang', 'Isi|Banyak',  'Protein|Sedang',        'Kandungan Gizi|Sangat Banyak','Kualitas|Sangat Baik']],
            ];

            foreach ($rulesData as $r) {
                $kondisiIds = collect($r['kondisi'])
                    ->map(fn($key) => $subkriteriaMap[$key]->id)
                    ->values()
                    ->toArray();

                FuzzyRule::create([
                    'nama_rule'   => $r['nama'],
                    'suplemen_id' => $suplemenMap[$r['suplemen']]->id,
                    'kondisi'     => $kondisiIds,
                    'bobot_rule'  => 1.0,
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Sample data berhasil di-seed. 5 suplemen & 15 rules telah ditambahkan.'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }
}
