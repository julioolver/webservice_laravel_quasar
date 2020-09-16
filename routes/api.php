<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post(
    '/cadastro', function (Request $request) {
    return $request->all();
}
);

//Route::get('/', ['as' => 'home','uses' => 'SiteController@index']);

Route::group(
    ['prefix' => '/usuario', 'as' => 'usuario.'], function () {
    Route::middleware('auth:api')->get('/', ['as' => 'index', 'uses' => 'UsuarioController@index']);
    Route::post('/incluir', ['as' => 'create', 'uses' => 'UsuarioController@create']);
    Route::post('/login', ['as' => 'login', 'uses' => 'UsuarioController@login']);
    Route::post('/store', ['as' => 'store', 'uses' => 'UsuarioController@store']);
    Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'UsuarioController@edit']);
    Route::post('/update/{id}', ['as' => 'update', 'uses' => 'UsuarioController@update']);
    Route::get('/destroy/{id}', ['as' => 'destroy', 'uses' => 'UsuarioController@destroy']);
}
);

Route::middleware('auth:api')->get(
    '/user', function (Request $request) {
    return $request->user();
}
);
