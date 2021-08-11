<?php

namespace App\Curl;

use Illuminate\Support\Facades\Http;

class CurlOTPWa
{
	protected static $url, $param;

	public static function setUrl($url)
	{
		self::$url = $url;
	}

	public static function setParam($param)
	{
		self::$param = $param;
	}

	public static function requestGet()
	{
		$response = Http::withHeaders([
			'Content-Type' => 'application/json',
		])->get(self::$url);

		return $response;
	}

	public static function requestPost()
	{
		$response = Http::withHeaders([
			'accept'       => 'application/json',
		])->post(self::$url, self::$param);

		return $response;
	}
}