<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  \App\User   $user
     * @return mixed
     */
    public function authenticate(User $user) {
        try {
            $this->validate($this->request, [
                'username' => 'required',
                'password' => 'required'
            ]);

            $user = User::with('mahasiswa.gugus', 'mahasiswa.prodi', 'pemandu')
                    ->where('username', $this->request->username)
                    ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $isUploadLearning = false;

            if ($user->role == 'MHS') {
                if ($user->mahasiswa->face_recognition == 1) {
                    $isUploadLearning = true;
                }
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
                        'is_upload_learning' => $isUploadLearning,
                        'token'              => $token,
                    ],
                ]);
            }

            // Bad Request response
            return writeLog('Password salah');
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }
}
