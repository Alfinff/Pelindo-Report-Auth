<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AuthController extends Controller
{
    
    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function authenticate(User $user) {
        
        $this->validate($this->request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        try {

            $user = User::with('profile')->where('email', $this->request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            if ($this->request->fcm_token) {
                $user->update([
                    'fcm_token' => $this->request->fcm_token,
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
                'message' => 'Data tidak ditemukan',
                'code'    => 404,
            ]);
        } catch (\Throwable $th) {
            return writeLog('Password salah');
        }

    }
}
