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
	try {
		\Log::error($message);
		return response()->json([
			'success' => false,
			'message' => env('APP_DEBUG') ? $message : 'Terjadi kesalahan',
			'code'    => 500,
		]);
	} catch (Exception $e) {
		return false;
	}
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

		$dataUser = [
			'id'           	=> $user->id,
			'uuid'          => $user->uuid,
			'nama'          => $user->nama,
			'role'          => $user->role,
			'email'         => $user->email,
			'no_hp'         => $user->no_hp,
			'fcm_token'     => $user->fcm_token,
			'profile'       => ''
		];

		if($user->profile) {
			$dataUser['profile'] = [
				'uuid'             => $user->profile->uuid ?? '',
				'foto'             => $user->profile->foto ?? '',
				'tgllahir'         => $user->profile->tgllahir ?? '',
				'jenis_kelamin'    => $user->profile->jenis_kelamin ?? '',
				'alamat'           => $user->profile->alamat ?? '',
				'user_id'          => $user->profile->user_id ?? '',
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
		return response()->json(array('msg' => $e->getMessage(), 'success' => false));
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

function generateRandomString($length = 6) {
	return substr(str_shuffle(str_repeat($x = '1234567890', ceil($length / strlen($x)))), 1, $length);
}