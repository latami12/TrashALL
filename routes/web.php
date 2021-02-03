<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'verify' => true,
    'register' => false
]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/user', 'PengurusController@index')->name('user');
Route::get('/get/users', 'PengurusController@index');
Route::post('/create/users', 'PengurusController@create');

Route::resource('/admin', 'ProfileWebController');

Route::get('/get-sampah', 'SampahController@getSampah')->name('sampah');
Route::delete('/delete-sampah', 'SampahController@delete')->name('sampah');
Route::get('/get-saldo', 'SaldoController@getSaldo')->name('saldo');

Route::get('bendahara', 'BendaharaController@index');
