<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    echo 'API Pelindo Report - Auth & Profil';
});

$router->post('/login', 'AuthController@authenticate');
$router->post('/login/mobile', 'AuthController@authMobile');

$router->group(['middleware' => ['jwt.auth']], function() use ($router) {
    $router->get('/cek-status-login', function () {
        return response()->json([
            'success' => true,
            'message' => 'User login',
            'code'    => 200,
        ]);
    });

    $router->get('/decodetoken', 'AuthController@decodetoken');
});


$router->group(['prefix' => 'lupapassword'], function() use ($router) {
    $router->post('/kirimnohp', 'LupaPasswordController@kirimNoHp');
    $router->post('/kirimulangotp', 'LupaPasswordController@kirimOtp');
    $router->post('/cekotp', 'LupaPasswordController@cekOtp');
    $router->post('/ubahsandi', 'LupaPasswordController@ubahSandi');
});

$router->group(['prefix' => 'superadmin', 'middleware' => ['jwt.auth', 'role.superadmin']], function() use ($router) {
    $router->group(['prefix' => 'profile'], function() use ($router) {
        $router->get('/', 'ProfileController@show');
        $router->put('/', 'ProfileController@update');
    });

});

$router->group(['prefix' => 'supervisor', 'middleware' => ['jwt.auth', 'role.supervisor']], function() use ($router) {
    $router->group(['prefix' => 'profile'], function() use ($router) {
        $router->get('/', 'ProfileController@show');
        $router->put('/', 'ProfileController@update');
    });

});

$router->group(['prefix' => 'eos', 'middleware' => ['jwt.auth', 'role.eos']], function() use ($router) {
    $router->group(['prefix' => 'profile'], function() use ($router) {
        $router->get('/', 'ProfileController@show');
        $router->put('/', 'ProfileController@update');
    });

});
