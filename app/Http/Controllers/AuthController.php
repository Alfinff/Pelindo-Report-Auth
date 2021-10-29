<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    
    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function authenticate(User $user) 
    {

        $validator = Validator::make($this->request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        try {
            $user = User::with('profile', 'role')->where('email', $this->request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna belum terdaftar',
                    'code'    => 404,
                ]);
            }

            if(!($user->role == env('ROLE_SPV')) && !($user->role == env('ROLE_SPA'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna belum terdaftar',
                    'code'    => 404,
                ]);
            }

            if ($this->request->token) {
                $user->update([
                    'fcm_token' => $this->request->token,
                ]);
            }

            if (Hash::check($this->request->password, $user->password)) {
                $token = generateJwt($user);

                if (!$token) {
                    return writeLog('Terjadi kesalahan');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Akses token',
                    'code'    => 200,
                    'data'    => [
                        'role'               => $user->role,
                        'token'              => $token,
                    ],
                ]);
            } 

            return response()->json([
                'success' => false,
                'message' => 'Email Atau Password Salah',
                'code'    => 404,
            ]);
        } catch (\Throwable $th) {
            return writeLog('Password salah');
        }

    }

    public function authMobile(User $user) 
    {

        $validator = Validator::make($this->request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        try {
            $user = User::with('profile', 'role')->whereHas('role', function ($query) {
                $query->where('kode', env('ROLE_EOS'));
            })->where('email', $this->request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna belum terdaftar',
                    'code'    => 404,
                ]);
            }

            if ($this->request->token) {
                $user->update([
                    'fcm_token' => $this->request->token,
                ]);
            }

            if (Hash::check($this->request->password, $user->password)) {
                $token = generateJwt($user);

                if (!$token) {
                    return writeLog('Terjadi kesalahan');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Akses token',
                    'code'    => 200,
                    'data'    => [
                        'role'               => $user->role,
                        'token'              => $token,
                    ],
                ]);
            } 

            return response()->json([
                'success' => false,
                'message' => 'Email Atau Password Salah',
                'code'    => 404,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }

    }

    public function decodetoken(Request $request) {
        try {
            $decodeToken = parseJwt($this->request->header('Authorization'));

            return json_encode($decodeToken);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }

}
