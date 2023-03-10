<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){

        $validasi = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validasi->fails()) {
            return $this->error($validasi->errors()->first());
        }
        $user = User::where('email', $request->email)->first();
        if($user){
            if (password_verify($request->password, $user->password)) {
                return $this->succes($user, 'Selamat datang '.$user->name);
            } else {
                return $this->error('Password salah');
            }
        } 
            return $this->error('Pengguna tidak ditemukan');
    }

    public function register(Request $request){

        $validasi = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:9',
        ]);

        if ($validasi->fails()) {
            return $this->error($validasi->errors()->first());
        }

        $user = User::create(array_merge($request->all(),[
            'password' => bcrypt($request->password)
        ]));

        if ($user) {
            return $this->succes($user, $user->name.' berhasil registrasi');
        } else {
            return $this->error('Terjadi kesalahan');
        }
    }

    public function succes($data, $message = "succes"){
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function error($message){
        return response()->json([
            'code' => 200,
            'message' => $message
        ], 400);
    }
}