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

Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify'); // ambil semua user

Route::get('profile', 'API\ProfileController@index')->middleware('jwt.verify'); //ambil semua profile
Route::patch('profile/{id}', 'API\ProfileController@update')->middleware('jwt.verify'); // profile update


Route::prefix('nasabah')->namespace('API')->middleware('jwt.verify')->group(function(){
    Route::get('show-request-penjemputan', 'PenjemputanController@showRequestPenjemputan');
    Route::post('request-penjemputan', 'PenjemputanController@requestPenjemputan');
    Route::delete('cancel-penjemputan/{id}', 'PenjemputanController@batalkanRequestPenjemputan');  
});

Route::prefix('pengurus-satu')->namespace('API')->middleware('jwt.verify')->group(function(){
    Route::get('/show-request', 'PenyetoranController@showNasabahRequest');
    Route::get('/accept-request', 'PenyetoranController@acceptNasabahRequest');
    Route::post('/store', 'PenyetoranController@penyetoranNasabah');
    Route::get('/show-deposit', 'PenyetoranController@showPenyetoranNasabah');
    Route::get('/confirm-deposit/{penyetoran-id}', 'PenyetoranController@confirmDepositAsTransaksi');
});

Route::prefix('pengurus-dua')->namespace('API')->middleware('jwt.verify')->group(function(){
    Route::get('/show-pengepul', 'PenjualanController@showPengepul');
    Route::post('/sell', 'PenjualanController@sellToPengepul');
});