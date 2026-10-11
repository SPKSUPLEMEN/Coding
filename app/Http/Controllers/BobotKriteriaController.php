<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\BobotKriteria;
use RealRashid\SweetAlert\Facades\Alert;
use DB;

class BobotKriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode','ASC')->get(['id','kode','nama','jumlah_bobot','jumlah_eigen','rata_eigen']);
        $bobotKriteria = BobotKriteria::groupBy('kriteria1')->get(['kriteria1']);

        // Mapping tabel bobot kriteria //
        $bobot = [];
        foreach ($bobotKriteria as $value) {
            array_push($bobot, BobotKriteria::leftJoin('kriteria as satu', 'bobot_kriteria.kriteria1', 'satu.kode')
                                                ->leftJoin('kriteria as dua', 'bobot_kriteria.kriteria2', 'dua.kode')
                                                ->orderByRaw('bobot_kriteria.kriteria1 ASC, bobot_kriteria.kriteria2 ASC')
                                                ->where('kriteria1', $value->kriteria1)->get(['satu.nama','kriteria1','kriteria2','bobot','eigen']));
        }

        // dd($map);
        // return response()->json(['status'=>'success', 'data'=>$nilaiEigen],200);
        return view('pages.bobot-kriteria.index', compact('kriteria','bobot'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'kriteria_1' => 'required',
                'nilai' => 'required',
                'kriteria_2' => 'required'
            ]);
            
            $daftarKriteria = Kriteria::get(['id','kode']);

            // START // update nilai bobot
            if($request->kriteria_1 === $request->kriteria_2){
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
                BobotKriteria::where('kriteria1', $request->kriteria_1)->where('kriteria2', $request->kriteria_2)->update(['bobot' => 1]);
            }else{
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
                BobotKriteria::where('kriteria1', $request->kriteria_1)->where('kriteria2', $request->kriteria_2)->update(['bobot' => $request->nilai]);
                
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select kedua
                BobotKriteria::where('kriteria1', $request->kriteria_2)->where('kriteria2', $request->kriteria_1)->update(['bobot' => 1/$request->nilai]);
            }
            // END // update nilai bobot
            
            // // START // update jumlah bobot salah satu kriteria
            foreach ($daftarKriteria as $value) {
                $jumlahBobot = BobotKriteria::where('kriteria2', $value->kode)->sum('bobot');
                Kriteria::where('kode', $value->kode)->update([
                    'jumlah_bobot' => $jumlahBobot,
                ]);
            }
            // END // update jumlah bobot salah satu kriteria

            // START // update nilai eigen
            foreach ($daftarKriteria as $value) {
                $daftarBobotKriteria = BobotKriteria::where('kriteria2', $value->kode)->get();
                $jumlahBobot = BobotKriteria::where('kriteria2', $value->kode)->sum('bobot');
                foreach ($daftarBobotKriteria as $value) {
                    $temp = BobotKriteria::where('id',$value->id)->first();
                    // dd($temp->bobot,$jumlahBobot);
                    if($temp->bobot > 0) BobotKriteria::where('id',$value->id)->update(['eigen' => $temp->bobot/$jumlahBobot]);
                }
            }
            // END // update nilai eigen

            // START // update jumlah dan rata-rata eigen
            foreach ($daftarKriteria as $value) {
                $jumlahEigen = BobotKriteria::where('kriteria1', $value->kode)->sum('eigen');
                Kriteria::where('kode', $value->kode)->update([
                    'jumlah_eigen' => $jumlahEigen,
                    'rata_eigen' => $jumlahEigen/sizeof($daftarKriteria)
                ]);
            }
            // END // update jumlah  dan rata-rata eigen

            Alert::success('Berhasil', "Bobot berhasil disimpan");
            DB::commit();

            return redirect()->route('bobot-kriteria.index');
        } catch (\Throwable $th) {
            //throw $th;
            Alert::error('Terjadi kesalahan', $th->getMessage());
            DB::rollback();
            return back();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        //
    }
}
