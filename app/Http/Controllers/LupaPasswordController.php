<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
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
        $this->validate($this->request, [
            'no_hp' => 'required',
        ]);

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
        $this->validate($this->request, [
            'no_hp' => 'required',
        ]);

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

            CurlOTPWa::setUrl(env('BASE_URL_CHAT') . 'login');
            CurlOTPWa::setBody([
                'user'     => $nik,
                'password' => $phone,
            ]);


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

    // public function cekOTP(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'otp'   => 'required|numeric|regex:/^[a-zA-Z0-9]+$/u'
    //     ]);

    //     if ($validator->fails()) {
    //         \Log::error($validator->errors());
    //         return response()->json([
    //             'meta' => [
    //                 'success' => false,
    //                 'message' => 'Validasi Gagal',
    //                 'code'    => Response::HTTP_OK,
    //             ]
    //         ], Response::HTTP_OK);
    //     } else {
    //         $email  = $request->email;
    //         $otp = $request->otp;

    //         DB::beginTransaction();
    //         try {
    //             $stmt = collect(DB::select("select * from nasabah where email = ? and otp = ? ", [$email, $otp]))->first();

    //             if (!$stmt) {
    //                 return response()->json([
    //                     'meta' => [
    //                         'success' => false,
    //                         'message' => 'OTP Tidak Sesuai',
    //                         'code'    => Response::HTTP_OK,
    //                     ]
    //                 ], Response::HTTP_OK);
    //             } else {
    //                 $id   = $stmt->id;
    //                 $hp   = $stmt->phone;
    //                 $nama = $stmt->nama;
    //                 $wa   = $stmt->wa;
    //                 $nik  = $stmt->nik;

    //                 // DB::update("update nasabah set status='1' where id='$id'");

    //                 DB::commit();
    //                 return response()->json([
    //                     'meta' => [
    //                         'success' => true,
    //                         'message' => 'Successfully',
    //                         'code'    => Response::HTTP_OK,
    //                     ],
    //                     'data' => array(
    //                         'id'     => $id,
    //                         'nama'   => $nama,
    //                         'pesan'  => 'OTP sesuai',
    //                         'topage' => 'setpassword'
    //                     )
    //                 ], Response::HTTP_OK);
    //             }
    //         } catch (\Throwable $th) {
    //             DB::rollback();
    //             return response()->json([
    //                 'meta' => [
    //                     'success' => false,
    //                     // 'message' => $th->getMessage(),
    //                     'message' => 'Terjadi Kesalahan',
    //                     'code'    => Response::HTTP_OK,
    //                 ]
    //             ], Response::HTTP_OK);
    //         }
    //     }
    // }

    // public function setPassword(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email'    => 'required|email',
    //         'newpassword' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'meta' => [
    //                 'success' => false,
    //                 'message' => 'Validasi Gagal',
    //                 'code'    => Response::HTTP_OK,
    //             ]
    //         ], Response::HTTP_OK);
    //     } else {
    //         $email = $request->email;
    //         $newpassword = Fungsi::encrypt($request->newpassword);

    //         DB::beginTransaction();
    //         try {
    //             $stmt = collect(DB::select("select * from nasabah where email = ?", [$email]))->first();

    //             if (!$stmt) {
    //                 return response()->json([
    //                     'meta' => [
    //                         'success' => false,
    //                         'message' => 'Data Tidak Ditemukan',
    //                         'code'    => Response::HTTP_OK,
    //                     ]
    //                 ], Response::HTTP_OK);
    //             } else {
    //                 $id    = $stmt->id;
    //                 $email = $stmt->email;
    //                 $nama  = $stmt->nama;
    //                 $wa    = $stmt->wa;

    //                 DB::update("update nasabah set pass = ? where id = ? ", [$newpassword, $id]);
    //                 // $pesan = 'Yth. ' . $nama . ', ' . $pin . ' adalah pin transaksi '.env('NamaBUMDesa');
    //                 // DB::connection('primakom')->insert("insert into outbox (nohp,pesan) values('$hp','$pesan')");
    //                 // DB::connection('primakom')->insert("insert into pesanwa (nohp,pesan1,pesan2,namafile,caption,respon,status) values('$wa','$pesan','','','','','0')");

    //                 DB::commit();
    //                 return response()->json([
    //                     'meta' => [
    //                         'success' => true,
    //                         'message' => 'Successfully',
    //                         'code'    => Response::HTTP_OK,
    //                     ],
    //                     'data' => array(
    //                         'id'     => $id,
    //                         'email'  => $email,
    //                         'pesan'  => 'Penggantian Password Berhasil',
    //                         'topage' => 'login'
    //                     ),
    //                 ], Response::HTTP_OK);
    //             }
    //         } catch (\Throwable $th) {
    //             DB::rollback();
    //             return response()->json([
    //                 'meta' => [
    //                     'success' => false,
    //                     // 'message' => $th->getMessage(),
    //                     'message' => 'Terjadi Kesalahan',
    //                     'code'    => Response::HTTP_OK,
    //                 ]
    //             ], Response::HTTP_OK);
    //         }
    //     }
    // }
}
