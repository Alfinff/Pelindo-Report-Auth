<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use GrahamCampbell\Flysystem\Facades\Flysystem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;
use Illuminate\Support\Facades\Hash;
// use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
   
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(Request $request)
    {

    }

    public function show()
    {
        try {
            $decodeToken = parseJwt($this->request->header('Authorization'));
            $uuid = $decodeToken->user->uuid;
            $user = Profile::with('user', 'user.role')->where('user_id', $uuid)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Profil Pengguna',
                'code'    => 200,
                'data'    => $user,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }

    public function update(Request $request)
    {

        // jangan pakai $this->validate
        $validator = Validator::make($this->request->all(), [
            'nama' => 'required',
            'no_hp' => 'required',
            // 'tgllahir' => 'required',
            // 'jenis_kelamin' => 'required',
            'alamat' => 'required',
            // 'foto'   => 'required',
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        DB::beginTransaction();
        try {
            $decodeToken = parseJwt($this->request->header('Authorization'));
            $uuid        = $decodeToken->user->uuid;
            $user   = User::where('uuid', $uuid)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $ceknohp   = User::where('no_hp', $this->request->no_hp)->whereNotIn('uuid', [$uuid])->first();
            if ($ceknohp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor hp tersebut sudah digunakan, silahkan gunakan nomor lain',
                    'code'    => 404,
                ]);
            }

            $user->update([
                'nama' => $this->request->nama,
                'no_hp' => $this->request->no_hp,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $user   = Profile::where('user_id', $uuid)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $pathfoto = $user->namafotoasli;
            if ($this->request->foto) {
                $foto     = base64_decode($this->request->foto);
                $pathfoto = 'profile/foto/'. $uuid.'.png';
                $upload   = Flysystem::connection('awss3')->put($pathfoto, $foto);
            } 

            $user->update([
                // 'tgllahir' => date('Y-m-d', strtotime($this->request->tgllahir)),
                // 'jenis_kelamin' => $this->request->jenis_kelamin,
                'alamat' => $this->request->alamat,
                'foto'   => $pathfoto,
            ]);

            $user = User::with('profile', 'role')->where('uuid', $uuid)->first();
            $aksesToken = generateJwt($user);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil ubah profil',
                'code'    => 200,
                'data'    => [
                    'akses_token' => $aksesToken,
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());
        }
    }

}
