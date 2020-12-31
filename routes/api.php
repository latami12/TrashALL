<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'UserController@register'); // Register untuk nasabah
Route::post('login', 'UserController@login'); // login untuk nasabah, pengurus1, pengurus2

Route::get('user', 'UserController@getAuthenticatedUser'); // ambil semua user

Route::get('profile', 'ProfileController@index')->middleware('auth:api'); //ambil semua profile
Route::patch('profile/{id}', 'ProfileController@update')->middleware('auth:api'); // profile update

Route::post('penjemputan', 'API\PenjemputanController@requestPenjemputan')->middleware('auth:api');
Route::delete('penjemputan/{id}', 'API\PenjemputanController@batalkanRequestPenjemputan')->middleware('auth:api');