<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Curl\CurlOTPWa;
use App\Models\User;

class LupaPasswordController extends Controller
{
    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function kirimNoHp(Request $request)
    {

        $validator = Validator::make($this->request->all(), [
            'no_hp' => 'required',
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        DB::beginTransaction();
        try {        

            $no_hp = $this->request->no_hp;
            $user = User::with('profile')->where('no_hp', $no_hp)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $nama_user = ucwords($user->nama) ?? '';
            $otp = generateRandomString();
            $msg = $otp." adalah kode verifikasi untuk penggantian password";

            CurlOTPWa::setUrl(env('URL_WOOWA'));
            CurlOTPWa::setParam([
                'phone_no' => $no_hp,
                'key' => env('KEY_WOOWA'),
                'message' => $msg,
            ]);
            CurlOTPWa::requestPost();

            $update = $user->update([
                'otp' => $otp,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'OTP Telah Dikirim ke '.$no_hp,
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());
        }
    }

    public function kirimUlangOTP(Request $request)
    {

        $validator = Validator::make($this->request->all(), [
            'no_hp' => 'required',
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        DB::beginTransaction();
        try {
            $no_hp = $this->request->no_hp;
            $user = User::with('profile')->where('no_hp', $no_hp)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $nama_user = ucwords($user->nama) ?? '';
            $otp = generateRandomString();

            CurlOTPWa::setUrl(env('URL_WOOWA'));
            CurlOTPWa::setParam([
                'phone_no' => $no_hp,
                'key' => env('KEY_WOOWA'),
                'message' => $msg,
            ]);
            CurlOTPWa::requestPost();

            $update = $user->update([
                'otp' => $otp,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'OTP Telah Dikirim ke '.$no_hp,
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());   
        }
    }

    public function cekOtp()
    {

        $validator = Validator::make($this->request->all(), [
            'no_hp' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        try {
            $no_hp = $this->request->no_hp;
            $user  = User::where('no_hp', $no_hp)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                    'code'    => 404,
                ]);
            }


            if ($user->otp != $this->request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak cocok',
                    'code'    => 404,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP cocok',
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }

    public function ubahSandi()
    {

        $validator = Validator::make($this->request->all(), [
            'no_hp' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return writeLogValidation($validator->errors());
        }

        DB::beginTransaction();
        try {
            $no_hp = $this->request->no_hp;
            $user  = User::where('no_hp', $no_hp)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            if ((int)$user->reset_pswd_count > 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengubahan Password Sudah Melebihi Limit',
                    'code'    => 505,
                ]);
            }

            $count = (int)$user->reset_pswd_count+1;
            $user->update([
                'password' => Hash::make($this->request->password),
                'reset_pswd_count' => $count,
                'reset_pswd_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil ubah password',
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());
        }
    }
    
}
