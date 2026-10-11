<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\BobotSubkriteria;
use RealRashid\SweetAlert\Facades\Alert;
use DB, Validator;

class BobotSubkriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('created_at', 'ASC')->get(['id','kode','nama']);
        return view('pages.bobot-sub-kriteria.index', compact('kriteria'));
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
            
            $daftarSubkriteria = SubKriteria::where('kriteria_id',$request->kriteria_id)->get(['id','kode']);

            // START // update nilai bobot
            if($request->kriteria_1 === $request->kriteria_2){
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
                BobotSubkriteria::where('kriteria1', $request->kriteria_1)->where('kriteria2', $request->kriteria_2)->update(['bobot' => 1]);
            }else{
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select pertama
                BobotSubkriteria::where('kriteria1', $request->kriteria_1)->where('kriteria2', $request->kriteria_2)->update(['bobot' => $request->nilai]);
                
                // update nilai bobot sesuai dengan kriteria yg dipilih pada form select kedua
                BobotSubkriteria::where('kriteria1', $request->kriteria_2)->where('kriteria2', $request->kriteria_1)->update(['bobot' => 1/$request->nilai]);
            }
            // END // update nilai bobot
            
            // // START // update jumlah bobot salah satu kriteria
            foreach ($daftarSubkriteria as $value) {
                $jumlahBobot = BobotSubkriteria::where('kriteria2', $value->kode)->sum('bobot');
                SubKriteria::where('kode', $value->kode)->update([
                    'jumlah_bobot' => $jumlahBobot,
                ]);
            }
            // END // update jumlah bobot salah satu kriteria

            // START // update nilai eigen
            foreach ($daftarSubkriteria as $value) {
                $daftarBobotKriteria = BobotSubkriteria::where('kriteria2', $value->kode)->get();
                $jumlahBobot = BobotSubkriteria::where('kriteria2', $value->kode)->sum('bobot');
                foreach ($daftarBobotKriteria as $value) {
                    $temp = BobotSubkriteria::where('id',$value->id)->first();
                    // dd($temp->bobot,$jumlahBobot);
                    if($temp->bobot > 0) BobotSubkriteria::where('id',$value->id)->update(['eigen' => $temp->bobot/$jumlahBobot]);
                }
            }
            // END // update nilai eigen

            // START // update jumlah dan rata-rata eigen
            foreach ($daftarSubkriteria as $value) {
                $jumlahEigen = BobotSubkriteria::where('kriteria1', $value->kode)->sum('eigen');
                SubKriteria::where('kode', $value->kode)->update([
                    'jumlah_eigen' => $jumlahEigen,
                    'rata_eigen' => $jumlahEigen/sizeof($daftarSubkriteria)
                ]);
            }
            // END // update jumlah  dan rata-rata eigen

            Alert::success('Berhasil', "Bobot berhasil disimpan");
            DB::commit();

            return redirect()->route('bobot-sub-kriteria.edit',$request->kriteria_id);
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
        $kriteria = Kriteria::where('id', $id)->first(['nama']);
        $subkriteria = SubKriteria::where('kriteria_id', $id)->orderBy('kode','ASC')->get(['kode','nama','jumlah_bobot','jumlah_eigen','rata_eigen']);
        $bobotSubkriteria = BobotSubkriteria::where('kriteria_id', $id)->groupBy('kriteria1')->get(['kriteria1']);

        // Mapping tabel bobot subkriteria
        $bobot = [];
        foreach ($bobotSubkriteria as $value) {
            array_push($bobot, BobotSubkriteria::leftJoin('sub_kriteria as satu', 'bobot_subkriteria.kriteria1', 'satu.kode')
                                                ->leftJoin('sub_kriteria as dua', 'bobot_subkriteria.kriteria2', 'dua.kode')
                                                ->orderByRaw('bobot_subkriteria.kriteria1 ASC, bobot_subkriteria.kriteria2 ASC')
                                                ->where('kriteria1', $value->kriteria1)->get(['satu.nama','kriteria1','kriteria2','bobot','eigen']));
        }

        return view('pages.bobot-sub-kriteria.edit', compact('kriteria','subkriteria','bobot',));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
