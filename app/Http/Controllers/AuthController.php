<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use App\Models\ProfileUser;

class AuthController extends Controller
{
    public function login()
    {
        if(Auth::check()){ // jika sudah login, maka redirect ke halaman home
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if($request->ajax() || $request->wantsJson()){
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    public function lupaPassword(){
        return view('auth.lupaPassword');
    }

    public function verifyDataGuest(Request $request){
        $verif = ProfileUser::where([
         'nidn' => $request->nidn,
        'no_telp' => $request->no_telp,
        'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
        ])->first();

        if(!$verif){
            return response()->json([
                'status'=> false,
                'message'=> 'Tolong Koreksi kembali Data anda'
            ]);
        }
        return response()->json([
            'status'=> true,
            'message'=> 'Verifikasi Berhasil',
            'url'=>route('newPassword',['id'=>$verif->id_profile]),
        ]);
    }
    public function newPassword($id){
        $user = ProfileUser::find($id);
        return view('auth.newPassword' ,['user'=>$user]);
    }
    public function updatePassword(Request $request, $id){
    $request->validate([
        'password' => 'required|min:6|confirmed', // pastikan input name password_confirmation di form
    ]);

    $user = UserModel::find($id);
    if(!$user){
        return response()->json([
            'status'=> false,
            'message'=> 'Data User Tidak ditemukan'
        ]);
    }

    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'Password berhasil diupdate',
        'alert' => 'success',
    ]);
}
}
