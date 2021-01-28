<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'UserController@register'); // Register untuk nasabah
Route::post('login', 'UserController@login'); // login untuk nasabah, pengurus1, pengurus2

Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify'); // ambil semua user

Route::get('/sampah', 'API\SampahController@getSampah')->middleware('jwt.verify'); // ambil sampah
Route::get('/sampah-all', 'API\SampahController@allKategoriSampah')->middleware('jwt.verify'); // ambil sampah

Route::get('profile', 'API\ProfileController@index')->middleware('jwt.verify'); //ambil semua profile
Route::patch('profile/{id}', 'API\ProfileController@update')->middleware('jwt.verify'); // profile update


Route::prefix('nasabah')->namespace('API')->middleware('jwt.verify')->group(function () {
    Route::get('show-request', 'PenjemputanController@showRequestPenjemputan'); // ngambil request
    Route::post('request', 'PenjemputanController@requestPenjemputan'); // request epnjemputan sampah nya ke pengurus satu
    Route::delete('request/cancel/{penjemputan_id}', 'PenjemputanController@batalkanRequestPenjemputan'); // cancel penjemputan
    Route::delete('request/cancel-item/{detail_penjemputan_id}', 'PenjemputanController@batalkanBarangRequestPenjemputan'); // cancel sampah penjemputannya

    Route::get('tabungan', 'API\NasabahController@getTabungan');
});

Route::prefix('pengurus-satu')->namespace('API')->middleware('jwt.verify')->group(function () {
    Route::get('/show-request', 'PenyetoranController@showNasabahRequest'); // tampilkan request dari nasabah
    Route::get('/show-accepted-request', 'PenyetoranController@showAcceptedRequest'); // tampilkan
    Route::get('/accept-request/{penjemputan_id}', 'PenyetoranController@acceptNasabahRequest'); //
    Route::get('/decline-request/{penjemputan_id}', 'PenyetoranController@declineNasabahRequest');
    Route::get('/search-nasabah/{keyword?}', 'PenyetoranController@searchNasabah'); // Optional Parameter
    Route::post('/store', 'PenyetoranController@penyetoranNasabah');
    Route::get('/show-deposit', 'PenyetoranController@showPenyetoranNasabah');
    Route::get('/confirm-deposit/{penyetoran_id}', 'PenyetoranController@confirmDepositAsTransaksi');
});

Route::prefix('pengurus-dua')->namespace('API')->middleware('jwt.verify')->group(function () {
    Route::get('/show-pengepul', 'PenjualanController@showPengepul');
    Route::post('/sell', 'PenjualanController@sellToPengepul');
});
