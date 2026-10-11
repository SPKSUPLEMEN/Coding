<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\BobotSubkriteria;
use DB, DataTables, Validator;

class SubKriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode', 'ASC')->get(['id', 'nama']);
        return view('pages.sub-kriteria.index', compact('kriteria'));
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $rules = [
                'kriteria_id' => 'required',
                'nama' => 'required'
            ];
    
            $messages  = [
                'required' => 'Tidak boleh kosong. ',
            ];

            $messages  = [
                'kriteria_id.required' => 'Kriteria : Tidak boleh kosong.',
                'nama.required' => 'Nama : Tidak boleh kosong.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
        
            if($validator->fails()) {
                return response()->json(['status'=>'validation error','message'=>$validator->messages()],400);
            }else{         
                
                $latest = SubKriteria::orderBy('created_at', 'DESC')->first();
                $number = !$latest ? 1 : $latest->kode+1;

                SubKriteria::create([
                    'kriteria_id' => $request->kriteria_id,
                    'kode' => $number,
                    'nama' => $request->nama,
                ]);
    
                $daftarSubkriteria = SubKriteria::where('kriteria_id',$request->kriteria_id)->get(['id','kode']);
    
                foreach($daftarSubkriteria as $value) {
                    foreach($daftarSubkriteria as $item) {
                        // cek jika sudah ada subkriteria yg sama
                        $check = BobotSubkriteria::where('subkriteria_id',$value->id)
                                                    ->where('kriteria1',$value->kode)
                                                    ->where('kriteria2',$item->kode)->first();
                        if(!$check){
                            BobotSubkriteria::create([
                                'kriteria_id' => $request->kriteria_id,
                                'subkriteria_id' => $value->id,
                                'kriteria1' => $value->kode,
                                'kriteria2' => $item->kode,
                                'bobot' => 0
                            ]);
                        }
                    }
                }
                
                DB::commit();
                return response()->json(['status'=>'success', 'message'=>'Berhasil disimpan.'],200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        try {
            $data = SubKriteria::where('id',$id)->first(['id','kode','nama']);
            return response()->json(['status'=>'success', 'data'=>$data],200); 
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'nama' => 'required',
        ];

        $messages  = [
            'nama.required' => 'Nama : Tidak boleh kosong.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if($validator->fails()) {
            return response()->json(['status'=>'validation error','message'=>$validator->messages()],400);
        }else{   
            DB::beginTransaction();
    
            try {
                $data = SubKriteria::find($id);
    
                $data->update([
                    'nama' => $request->nama,
                ]);
    
                DB::commit();
                return response()->json(['status'=>'success', 'message'=>'Berhasil disimpan.'],200);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollback();
                return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
            }
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $data = SubKriteria::find($id);

            BobotSubkriteria::where('kriteria1',$data->kode)->orWhere('kriteria2',$data->kode)->delete();
            $data->delete();

            $this->updatePerhitunganAhp($data->kriteria_id);
            
            DB::commit();
            return response()->json(['status'=>'success', 'message'=>'Berhasil dihapus.'],200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function updatePerhitunganAhp($idKriteria){
        $daftarSubkriteria = SubKriteria::where('kriteria_id',$idKriteria)->get(['id','kode']);
        
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
    }

    public function datatable(){
        $data = DB::table('sub_kriteria')->orderByRaw('kriteria.kode ASC, sub_kriteria.created_at ASC')
        ->leftJoin('kriteria', 'sub_kriteria.kriteria_id', 'kriteria.id')
        ->select(
            'sub_kriteria.id',
            'sub_kriteria.kode',
            'sub_kriteria.nama',
            'kriteria.nama as nama_kriteria'
        )->get();

        return Datatables::of($data)->addIndexColumn()
            ->addIndexColumn()
            ->addColumn('nama_kriteria', function ($data) {
                return $data->nama_kriteria;
            })
            ->addColumn('kode', function ($data) {
                return "K-".$data->kode."";
            })
            ->addColumn('nama', function ($data) {
                return $data->nama;
            })
            ->addColumn('action', function($data){
                return '
                    <a href="javascript:void(0)" data-toggle="tooltip" 
                        data-id="'.$data->id.'" 
                        data-nama="'.$data->nama.'" 
                        data-original-title="Edit" class="edit btn btn-primary btn-sm show-edit-modal"><i class="fas fa-edit"></i></a>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm show-delete-modal"><i class="fas fa-trash-alt"></i></a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
