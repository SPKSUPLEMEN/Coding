<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use DB, Validator;
use Yajra\DataTables\Facades\DataTables;

class KriteriaController extends Controller
{

    public function index()
    {
        return view('pages.kriteria.index');
    }

    // -------------------------------------------------------
    // KRITERIA
    // -------------------------------------------------------

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:100',
            ], [
                'nama.required' => 'Nama kriteria tidak boleh kosong.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation error', 'message' => $validator->messages()], 400);
            }

            $maxKode = Kriteria::max('kode');
            $kode    = $maxKode ? $maxKode + 1 : 1;

            Kriteria::create([
                'kode' => $kode,
                'nama' => $request->nama,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kriteria berhasil disimpan.'], 200);

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
                'nama' => 'required|string|max:100',
            ], [
                'nama.required' => 'Nama kriteria tidak boleh kosong.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation error', 'message' => $validator->messages()], 400);
            }

            Kriteria::where('id', $id)->update(['nama' => $request->nama]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kriteria berhasil diperbarui.'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Hapus semua subkriteria terkait
            SubKriteria::where('kriteria_id', $id)->delete();

            Kriteria::find($id)->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kriteria berhasil dihapus.'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function datatable()
    {
        $data = DB::table('kriteria')
            ->orderBy('created_at', 'ASC')
            ->select('id', 'kode', 'nama')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('kode', fn($d) => 'K-' . $d->kode)
            ->addColumn('nama', fn($d) => $d->nama)
            ->addColumn('action', function ($d) {
                return '
                    <a href="javascript:void(0)" data-id="' . $d->id . '" data-nama="' . $d->nama . '"
                        class="edit btn btn-primary btn-sm show-edit-modal"><i class="fas fa-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $d->id . '"
                        class="btn btn-danger btn-sm show-delete-modal"><i class="fas fa-trash-alt"></i></a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // -------------------------------------------------------
    // SUB KRITERIA (Himpunan Fuzzy)
    // -------------------------------------------------------

    /**
     * Simpan SubKriteria beserta batas membership function (fungsi segitiga).
     * batas_bawah < batas_tengah < batas_atas  dengan range 0–10
     *
     * Contoh untuk Harga:
     *   Murah  : bawah=0, tengah=1, atas=4
     *   Sedang : bawah=3, tengah=5, atas=7
     *   Mahal  : bawah=6, tengah=9, atas=10
     */
    public function storeSubkriteria(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'kriteria_id'  => 'required|exists:kriteria,id',
                'nama'         => 'required|string|max:100',
                'batas_bawah'  => 'required|numeric|min:0|max:10',
                'batas_tengah' => 'required|numeric|min:0|max:10',
                'batas_atas'   => 'required|numeric|min:0|max:10',
            ], [
                'kriteria_id.required'  => 'Kriteria wajib dipilih.',
                'nama.required'         => 'Nama himpunan fuzzy tidak boleh kosong.',
                'batas_bawah.required'  => 'Batas bawah wajib diisi.',
                'batas_tengah.required' => 'Batas tengah wajib diisi.',
                'batas_atas.required'   => 'Batas atas wajib diisi.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation error', 'message' => $validator->messages()], 400);
            }

            // Validasi urutan batas
            if ($request->batas_bawah > $request->batas_tengah || $request->batas_tengah > $request->batas_atas) {
                return response()->json([
                    'status'  => 'validation error',
                    'message' => 'Urutan batas harus: batas bawah ≤ batas tengah ≤ batas atas.',
                ], 400);
            }

            $latest = SubKriteria::where('kriteria_id', $request->kriteria_id)->orderBy('created_at', 'DESC')->first();
            $kode   = !$latest ? 1 : $latest->kode + 1;

            SubKriteria::create([
                'kriteria_id'  => $request->kriteria_id,
                'kode'         => $kode,
                'nama'         => $request->nama,
                'batas_bawah'  => $request->batas_bawah,
                'batas_tengah' => $request->batas_tengah,
                'batas_atas'   => $request->batas_atas,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Himpunan fuzzy berhasil disimpan.'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function updateSubkriteria(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama'         => 'required|string|max:100',
                'batas_bawah'  => 'required|numeric|min:0|max:10',
                'batas_tengah' => 'required|numeric|min:0|max:10',
                'batas_atas'   => 'required|numeric|min:0|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'validation error', 'message' => $validator->messages()], 400);
            }

            if ($request->batas_bawah > $request->batas_tengah || $request->batas_tengah > $request->batas_atas) {
                return response()->json([
                    'status'  => 'validation error',
                    'message' => 'Urutan batas harus: batas bawah ≤ batas tengah ≤ batas atas.',
                ], 400);
            }

            SubKriteria::where('id', $id)->update([
                'nama'         => $request->nama,
                'batas_bawah'  => $request->batas_bawah,
                'batas_tengah' => $request->batas_tengah,
                'batas_atas'   => $request->batas_atas,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Himpunan fuzzy berhasil diperbarui.'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function destroySubkriteria($id)
    {
        DB::beginTransaction();

        try {
            SubKriteria::find($id)->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Himpunan fuzzy berhasil dihapus.'], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function datatableSubkriteria($kriteriaId)
    {
        $data = DB::table('sub_kriteria')
            ->where('kriteria_id', $kriteriaId)
            ->orderBy('created_at', 'ASC')
            ->select('id', 'kode', 'nama', 'batas_bawah', 'batas_tengah', 'batas_atas')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($d) {
                return '
                    <a href="javascript:void(0)" data-id="' . $d->id . '" data-nama="' . $d->nama . '"
                        data-batas_bawah="' . $d->batas_bawah . '" data-batas_tengah="' . $d->batas_tengah . '"
                        data-batas_atas="' . $d->batas_atas . '"
                        class="edit btn btn-primary btn-sm show-edit-subkriteria"><i class="fas fa-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $d->id . '"
                        class="btn btn-danger btn-sm show-delete-subkriteria"><i class="fas fa-trash-alt"></i></a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function datatableAll(Request $request)
    {
        $query = DB::table('sub_kriteria')
            ->join('kriteria', 'sub_kriteria.kriteria_id', '=', 'kriteria.id')
            ->orderBy('kriteria.kode', 'ASC')
            ->orderBy('sub_kriteria.kode', 'ASC')
            ->select(
                'sub_kriteria.id',
                'sub_kriteria.kriteria_id',
                'sub_kriteria.kode',
                'sub_kriteria.nama',
                'sub_kriteria.batas_bawah',
                'sub_kriteria.batas_tengah',
                'sub_kriteria.batas_atas',
                'kriteria.nama as nama_kriteria'
            );

        if ($request->filled('kriteria_id')) {
            $query->where('sub_kriteria.kriteria_id', $request->kriteria_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_kriteria', fn($d) => $d->nama_kriteria)
            ->addColumn('visualisasi', function ($d) {
                $a = $d->batas_bawah;
                $b = $d->batas_tengah;
                $c = $d->batas_atas;
                return "<span class='badge bg-light text-dark border'>{$a} | {$b} | {$c}</span>";
            })
            ->addColumn('action', function ($d) {
                return '<div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-primary btn-sm btn-edit-sub"
                        data-id="'.$d->id.'"
                        data-kriteria_id="'.$d->kriteria_id.'"
                        data-nama="'.$d->nama.'"
                        data-batas_bawah="'.$d->batas_bawah.'"
                        data-batas_tengah="'.$d->batas_tengah.'"
                        data-batas_atas="'.$d->batas_atas.'"
                        title="Edit"><i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete-sub"
                        data-id="'.$d->id.'" title="Hapus"><i class="fas fa-trash-alt"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['visualisasi', 'action'])
            ->make(true);
    }
}