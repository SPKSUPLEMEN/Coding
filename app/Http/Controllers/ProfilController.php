<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use DB, Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $data = User::where('users.id',Auth::id())->first();

        return view('pages.profil.index',compact('data'));
    }

    public function ubahProfil(Request $request){
        DB::beginTransaction();
        
        $rules = [
            'name' => 'required',
        ];

        if($request->password) $rules += ['password'=>'min:8|confirmed'];

        $request->validate($rules,
        [
            'required' => 'Tidak boleh kosong.',
            'min' => 'Minimal :min karakter. ',
            'confirmed' => 'Konfirmasi sandi tidak cocok. '
        ]);

        try {
            $user = User::where('id',Auth::id())->first();
            $user->update([
                'name' => $request->name,
                'password' => bcrypt($request->password) ?? $user->password,
            ]);

            Alert::success('Berhasil', "Berhasil menyimpan.");
            DB::commit();
            return redirect()->route('profil');
        } catch (\Throwable $th) {
            //throw $th;

            Alert::error('Terjadi kesalahan', $th->getMessage());
            DB::rollback();
            return back();
        }
    }
}
