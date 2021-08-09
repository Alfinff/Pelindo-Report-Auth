<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

function writeLog($message)
{
	\Log::error($message);
	return response()->json([
		'success' => false,
		'message' => env('APP_DEBUG') ? $message : 'Terjadi kesalahan',
		'code'    => 500,
	]);
}

function generateUuid()
{
	try {
		return Uuid::uuid4();
	} catch (Exception $e) {
		return false;
	}
}

function generateJwt(User $user)
{
	try {
	    $key  = str_shuffle('QWERTYUIOPASDFGHJKLZXCVBNM1234567890!@#$%^&*()_={}|:"<>?[]\;');

		if($user->role == env('ROLE_MHS')) {
			$dataUser = [
				'id'               => $user->id,
				'uuid'             => $user->uuid,
				'role'             => $user->role,
				'username'         => $user->username,
				'mahasiswa'        => [
					'uuid'             => $user->mahasiswa->uuid,
					'user_id'          => $user->mahasiswa->user_id,
					'nama'             => $user->mahasiswa->nama,
					'nim'              => $user->mahasiswa->nim,
					'alamat'           => $user->mahasiswa->alamat,
					'gugus_id'         => $user->mahasiswa->gugus_id,
					'prodi_id'         => $user->mahasiswa->prodi_id,
					'tgllahir'         => $user->mahasiswa->tgllahir,
					'nohp'             => $user->mahasiswa->nohp,
					'notif_email'      => $user->mahasiswa->notif_email,
					'face_recognition' => $user->mahasiswa->face_recognition,
					'email'            => $user->mahasiswa->email,
					'foto'             => $user->mahasiswa->foto,
				],
				'gugus'            => [
					'uuid'       => $user->mahasiswa->gugus->uuid,
					'nama'       => $user->mahasiswa->gugus->name,
					'pemandu_id' => $user->mahasiswa->gugus->pemandu_id,
				],
				'prodi' => [
					'uuid' => $user->mahasiswa->prodi->uuid,
					'nama' => $user->mahasiswa->prodi->nama,
					'kode' => $user->mahasiswa->prodi->kode,
				],
			];
		} else {
			$dataUser = [
				'id'       => $user->id,
				'uuid'     => $user->uuid,
				'role'     => $user->role,
				'username' => $user->username,
			];
		}

	    $payload = [
			'iss'  => 'lumen-jwt',
			'iat'  => time(),
			'exp'  => time() + 60 * 60,
			'key'  => $key,
			'user' => $dataUser,
	    ];

	    // find user
	    $user = User::find($user->id);

	    // update key
	    $user->update([
	        'key' => $key,
	    ]);

	    return JWT::encode($payload, env('JWT_SECRET'));
	} catch (Exception $e) {
		return false;
	}
}

function parseJwt($token)
{
	return JWT::decode($token, env('JWT_SECRET'), array('HS256'));
}

function uploadFileS3($base64, $path)
{
	$file = base64_decode($base64);
	Flysystem::connection('awss3')->put($path, $file);
}

function generateOtp()
{
	return substr(str_shuffle('1234567890'), 0, 6);
}

function formatTanggal($tanggal)
{
	return date('Y-m-d H:i:s', strtotime($tanggal));
}

function sendFcm($to, $notification, $data)
{
	$response = Http::withHeaders([
		'Authorization' => 'key=' . env('KEY_FCM'),
		'Content-Type'  => 'application/json',
	])->post(env('URL_FCM'), [
		'to'           => $to,
		'notification' => $notification,
		'data'         => $data,
	]);

	return $response;
}
