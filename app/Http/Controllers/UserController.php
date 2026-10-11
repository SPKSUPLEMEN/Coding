<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB, DataTables, Validator;

class UserController extends Controller
{

    public function index()
    {
        return view('pages.user.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
            ];
    
            $messages  = [
                'name.required' => 'Nama : Tidak boleh kosong.',
                'email.required' => 'Email : Tidak boleh kosong.',
                'email.email' => 'Email : Format tidak benar.',
                'email.unique' => 'Email : Telah digunakan.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
        
            if($validator->fails()) {
                return response()->json(['status'=>'validation error','message'=>$validator->messages()],400);
            }else{                 
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt("pengguna123"),
                    'role' => 'user'
                ]);

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
        //
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' .$id,
            ];
    
            $messages  = [
                'name.required' => 'Nama : Tidak boleh kosong.',
                'email.required' => 'Email : Tidak boleh kosong.',
                'email.email' => 'Email : Format tidak benar.',
                'email.unique' => 'Email : Telah digunakan.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
        
            if($validator->fails()) {
                return response()->json(['status'=>'validation error','message'=>$validator->messages()],400);
            }else{                
                User::where('id',$id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
    
                DB::commit();
                return response()->json(['status'=>'success', 'message'=>'Berhasil disimpan.'],200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['status'=>'failed','message'=>$th->getMessage()],500);
        }
    }

    public function destroy($id)
    {
        //
    }

    public function datatable(){
        // mengambil data
        $userList = DB::table('users')
                        ->where('role','mhs')
                        ->orderBy('created_at', 'DESC')
                        ->select(
                            'id',
                            'name',
                            'email',
                        )->get();

        return Datatables::of($userList)->addIndexColumn()
            ->addIndexColumn()
            ->addColumn('name', function ($userList) {
                return $userList->name;
            })
            ->addColumn('email', function ($userList) {
                return $userList->email;
            })
            ->addColumn('action', function($userList){
                return '
                    <a href="javascript:void(0)" data-toggle="tooltip" 
                        data-id="'.$userList->id.'"
                        data-nama="'.$userList->name.'" 
                        data-email="'.$userList->email.'" 
                        data-original-title="Edit" class="edit btn btn-primary btn-sm show-edit-modal"><i class="fas fa-edit"></i></a>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$userList->id.'" data-original-title="Delete" class="btn btn-danger btn-sm show-delete-modal"><i class="fas fa-trash-alt"></i></a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
