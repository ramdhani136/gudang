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

Route::resource('barang','Barang\BarangController');
Route::resource('customer','Customer\CustomerController');
Route::resource('divisi','Divisi\DivisiController');
Route::resource('rso','Rso\RsoController');
Route::get('rso/data/purch/','Rso\RsoController@purch');
Route::get('listrso/data/purch/{id}','Rso\ListRsoController@purch');
Route::get('listrso/data/acc/{no}','Rso\ListRsoController@purchacc');
Route::get('listrso/data/dic/{no}','Rso\ListRsoController@dicacc');
Route::put('listrso/data/booking/{no}','Rso\ListRsoController@booking');
Route::put('listrso/data/sopen/{no}','Rso\ListRsoController@sOpen');
Route::get('listrso/data/group','Rso\ListRsoController@group');
Route::get('listrso/data/listpo/{po}','Rso\ListRsoController@listpo');
Route::get('listrso/data/group/{barang}','Rso\ListRsoController@grouplist');
Route::get('listrso/data/groupopen/{po}/{barang}','Rso\ListRsoController@groupopen');
Route::resource('supplier','Supplier\SupplierController');
Route::resource('listrso','Rso\ListRsoController');
Route::resource('sales','Sales\SalesController');
Route::resource('so','So\SoController');
Route::resource('custprice','sales\CustpriceController');
Route::get('data/price/{customer}/{barang}','Sales\CustpriceController@viewprice');
Route::get('view/price/{customer}/{barang}','Sales\CustpriceController@ambilPrice');
Route::resource('purchasing','Purchase\PurchasingController');
Route::resource('po','Po\PoController');


