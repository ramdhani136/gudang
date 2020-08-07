<?php

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

Auth::routes();


Route::view('/','home')->middleware('auth');
Route::view('/{any}','home')->middleware('auth');
Route::view('/{any}/{any1}/{any2}','home')->middleware('auth');
Route::view('/{any}/{any1}/{any2}/{any3}','home')->middleware('auth');
Route::view('/{any}/{any1}/{any2}/{any3}/{any4}','home')->middleware('auth');
Route::view('/{any}/{any3}','home')->middleware('auth');
