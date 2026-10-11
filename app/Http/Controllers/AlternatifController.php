<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\FuzzyRule;
use DB, Validator;
use Yajra\DataTables\Facades\DataTables;

class AlternatifController extends Controller
{
    public function index()
    {
        $totalProduk = Alternatif::count();
        return view('pages.alternatif.index', compact('totalProduk'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama'      => 'required|string|max:150',
                'deskripsi' => 'nullable|string|max:500',
            ], [
                'nama.required' => 'Nama produk tidak boleh kosong.',
                'nama.max'      => 'Nama produk maksimal 150 karakter.',
                'deskripsi.max' => 'Deskripsi maksimal 500 karakter.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'validation error',
                    'message' => $validator->messages()
                ], 400);
            }

            Alternatif::create([
                'nama'      => trim($request->nama),
                'deskripsi' => trim($request->deskripsi ?? ''),
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Produk suplemen berhasil ditambahkan.'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $data = Alternatif::find($id);

        if (!$data) {
            return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Hitung jumlah rule yang menggunakan produk ini
        $jumlahRule = FuzzyRule::where('suplemen_id', $id)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'          => $data->id,
                'nama'        => $data->nama,
                'deskripsi'   => $data->deskripsi,
                'jumlah_rule' => $jumlahRule,
                'created_at'  => $data->created_at->format('d M Y'),
            ]
        ], 200);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nama'      => 'required|string|max:150',
                'deskripsi' => 'nullable|string|max:500',
            ], [
                'nama.required' => 'Nama produk tidak boleh kosong.',
                'nama.max'      => 'Nama produk maksimal 150 karakter.',
                'deskripsi.max' => 'Deskripsi maksimal 500 karakter.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'validation error',
                    'message' => $validator->messages()
                ], 400);
            }

            $data = Alternatif::find($id);

            if (!$data) {
                return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
            }

            $data->update([
                'nama'      => trim($request->nama),
                'deskripsi' => trim($request->deskripsi ?? ''),
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Produk suplemen berhasil diperbarui.'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $data = Alternatif::find($id);

            if (!$data) {
                return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
            }

            // Cek apakah produk sedang digunakan oleh fuzzy rule
            $jumlahRule = FuzzyRule::where('suplemen_id', $id)->count();
            if ($jumlahRule > 0) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => "Produk ini tidak bisa dihapus karena masih digunakan oleh {$jumlahRule} fuzzy rule. Hapus rule terkait terlebih dahulu."
                ], 422);
            }

            $data->delete();

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Produk suplemen berhasil dihapus.'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
        }
    }

    public function datatable()
    {
        $data = DB::table('alternatif')
            ->leftJoin('fuzzy_rules', 'alternatif.id', '=', 'fuzzy_rules.suplemen_id')
            ->select(
                'alternatif.id',
                'alternatif.nama',
                'alternatif.deskripsi',
                'alternatif.created_at',
                DB::raw('COUNT(fuzzy_rules.id) as jumlah_rule')
            )
            ->groupBy('alternatif.id', 'alternatif.nama', 'alternatif.deskripsi', 'alternatif.created_at')
            ->orderBy('alternatif.created_at', 'ASC');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', fn($d) => $d->nama)
            ->addColumn('deskripsi', function ($d) {
                $desc = $d->deskripsi ?: '-';
                return strlen($desc) > 60
                    ? '<span title="' . htmlspecialchars($d->deskripsi) . '">' . substr($desc, 0, 60) . '...</span>'
                    : $desc;
            })
            ->addColumn('jumlah_rule', function ($d) {
                $badge = $d->jumlah_rule > 0
                    ? '<span class="badge bg-primary">' . $d->jumlah_rule . ' rule</span>'
                    : '<span class="badge bg-secondary">0 rule</span>';
                return $badge;
            })
            ->addColumn('action', function ($d) {
                return '
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-info btn-sm btn-detail"
                        data-id="' . $d->id . '" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-primary btn-sm btn-edit"
                        data-id="' . $d->id . '"
                        data-nama="' . htmlspecialchars($d->nama) . '"
                        data-deskripsi="' . htmlspecialchars($d->deskripsi ?? '') . '"
                        title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete"
                        data-id="' . $d->id . '"
                        data-nama="' . htmlspecialchars($d->nama) . '"
                        data-rule="' . $d->jumlah_rule . '"
                        title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['deskripsi', 'jumlah_rule', 'action'])
            ->make(true);
    }
}