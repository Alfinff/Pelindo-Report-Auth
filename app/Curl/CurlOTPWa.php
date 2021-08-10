<?php

namespace App\Curl;

use Illuminate\Support\Facades\Http;

class CurlOTPWa
{
	protected static $url, $param, $body, $token, $userId;

	public static function setUrl($url)
	{
		self::$url = $url;
	}

	public static function setParam($param)
	{
		self::$param = $param;
	}

	public static function setBody($body)
	{
		self::$body = $body;
	}

	public static function setToken($token)
	{
		self::$token = $token;
	}

	public static function setUserId($userId)
	{
		self::$userId = $userId;
	}

	public static function requestGet()
	{
		$response = Http::withHeaders([
			'X-Auth-Token' => self::$token,
			'X-User-Id'    => self::$userId,
			'Content-Type' => 'application/json',
		])->get(self::$url);

		return $response;
	}

	public static function requestPost()
	{
		$response = Http::withHeaders([
			'X-Auth-Token' => self::$token,
			'X-User-Id'    => self::$userId,
			'accept'       => 'application/json',
		])->post(self::$url, self::$body);

		return $response;
	}
}