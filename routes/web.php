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

Route::get('/data/so/print/{so}','So\SoController@print')->middleware('auth');
Route::get('/data/formprice/print/{nomor}','Sales\FormpriceController@print')->middleware('auth');
Route::get('/data/rso/print/{rso}','Rso\RsoController@printtersedia')->middleware('auth');
Route::get('/data/rsott/print/{rso}','Rso\RsoController@printtdk')->middleware('auth');
Route::get('/data/po/print/{po}','Po\PoController@print')->middleware('auth');
Route::get('/data/bck/print/{bck}','Bck\BckController@print')->middleware('auth');




Route::group(['middleware' => 'auth'], function() {
    Route::get('/chats','ChatsController@index');
    Route::get('/messages','ChatsController@fetchmessage');
    Route::post('/messages','ChatsController@sendmessage');
});





Auth::routes();


Route::view('/','home')->middleware('auth');
Route::view('/{any}','home')->middleware('auth');
Route::view('/{any}/{any1}/{any2}','home')->middleware('auth');
Route::view('/{any}/{any1}/{any2}/{any3}','home')->middleware('auth');
Route::view('/{any}/{any1}/{any2}/{any3}/{any4}','home')->middleware('auth');
Route::view('/{any}/{any3}','home')->middleware('auth');


