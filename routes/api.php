<?php

use App\Model\Rso\Rso;
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
Route::get('barang/data/all/','Barang\BarangController@all');
Route::resource('customer','Customer\CustomerController');
Route::resource('divisi','Divisi\DivisiController');

Route::resource('rso','Rso\RsoController');
Route::get('rso/data/pr','Rso\RsoController@pr');
Route::get('rso/data/pr/{rso}','Rso\RsoController@viewpr');
Route::get('rso/data/realrso/','Rso\RsoController@realrso');
Route::get('rso/data/view/{groupso}','Rso\RsoController@groupso');

Route::get('rso/data/purch/','Rso\RsoController@purch');
Route::get('rso/data/confirm/','Rso\RsoController@confirm');
Route::get('rso/data/confirm/{groupso}','Rso\RsoController@confirmgroup');
Route::get('listrso/data/purch/{id}','Rso\ListRsoController@purch');
Route::get('listrso/data/pr/{rso}','Rso\ListRsoController@pr');
Route::get('listrso/data/acc/{no}','Rso\ListRsoController@purchacc');
Route::get('listrso/data/dic/{no}','Rso\ListRsoController@dicacc');
Route::get('listrso/data/sotersedia/{no}','Rso\ListRsoController@sotersedia');
Route::get('listrso/data/sott/{no}','Rso\ListRsoController@sott');
Route::put('listrso/data/booking/{no}','Rso\ListRsoController@booking');
Route::put('listrso/data/sopen/{no}','Rso\ListRsoController@sOpen');
Route::put('listrso/data/deletePo/{po}','Rso\ListRsoController@deletePo');
Route::get('listrso/data/group','Rso\ListRsoController@group');
Route::get('listrso/data/listpo/{po}','Rso\ListRsoController@listpo');
Route::get('listrso/data/pilihpo/{po}','Rso\ListRsoController@pilihpo');
Route::get('listrso/data/listclose/{po}','Rso\ListRsoController@listclose');
Route::get('listrso/data/listall/{po}','Rso\ListRsoController@listall');
Route::get('listrso/data/group/{barang}','Rso\ListRsoController@grouplist');
Route::get('listrso/data/{rso}/{barang}','Rso\ListRsoController@listcheckermasuk');
Route::get('listrso/data/groupopen/{po}/{barang}','Rso\ListRsoController@groupopen');
Route::put('listrso/data/{po}/{barang}','Rso\ListRsoController@statuspo');
Route::get('listrso/data/{rso}/{barang}','Rso\ListRsoController@viewlistso');
Route::resource('supplier','Supplier\SupplierController');
Route::resource('listrso','Rso\ListRsoController');
Route::resource('sales','Sales\SalesController');

Route::resource('so','So\SoController');
Route::get('so/data/realso','So\SoController@realso');
Route::get('so/laporan/data','So\SoController@laporan');
Route::get('so/data/aktif','So\SoController@aktif');
Route::get('so/selesai/kordinator/{groupso}','So\SoController@skordinator');
Route::get('so/selesai/supervisor','So\SoController@ssupervisor');
Route::get('so/rso/{rso}','So\SoController@statusopen');
Route::get('so/data/view/{groupso}','So\SoController@groupso');

// Route::get('so/kordisales/{groupso}','So\SoController@kordisales');
// Route::get('so/sales/{groupso}/{sales}','So\SoController@sales');

Route::resource('bcm','Barang\BcmController');
Route::get('bcm/data/open','Barang\BcmController@open');
Route::resource('custprice','sales\CustpriceController');
Route::get('data/price/{customer}/{barang}','Sales\CustpriceController@viewprice');
Route::get('view/price/{customer}/{barang}','Sales\CustpriceController@ambilPrice');
Route::get('view/price/aktif/{customer}/{barang}','Sales\CustpriceController@aktif');
Route::get('view/price/tolak/{customer}/{barang}','Sales\CustpriceController@tolak');
Route::get('view/price/tdkaktif/{customer}/{barang}','Sales\CustpriceController@tdkaktif');
Route::get('view/data/price/tdkaktif','Sales\CustpriceController@request');
Route::get('view/data/{nomor}','Sales\CustpriceController@view');
Route::resource('purchasing','Purchase\PurchasingController');
Route::resource('po','Po\PoController');
Route::get('poaktif/','Po\PoController@aktif');
Route::get('po/selesai/view','Po\PoController@rselesai');
Route::resource('listbcm','Barang\ListBcmController');
Route::get('listbcm/data/{bcm}/{barang}','Barang\ListBcmController@count');
Route::get('listbcm/data/{bcm}','Barang\ListBcmController@nnonaktif');
Route::get('view/detailpo/{po}/{barang}','Rso\ListRsoController@detailpo');
Route::get('view/accpo/{po}/{barang}','Rso\ListRsoController@accpo');

Route::get('rso/tersedia/{rso}','Rso\ListRsoController@tersedia');
Route::get('rso/tdktersedia/{rso}','Rso\ListRsoController@tdktersedia');

Route::resource('ekspedisi','Ekspedisi\EkspedisiController');

Route::resource('bbm','Bbm\BbmController');
Route::resource('listbbm','Bbm\ListBbmController');
Route::get('listbbm/data/{bbm}/{barang}','Bbm\ListBbmController@barang');


Route::resource('bck','Bck\BckController');

Route::get('bck/data/aktif','Bck\BckController@aktif');
Route::get('bck/data/so/{so}','Bck\BckController@so');

Route::resource('listbck','Bck\ListBckController');
Route::get('listbck/data/aktif/{bck}','Bck\ListBckController@aktif');
Route::get('listbck/view/{bck}/{barang}/{idx}','Bck\ListBckController@viewbbk');

Route::get('bcm/{barang}/{bcm}','Barang\ListBcmController@getbbm');

Route::resource('kendaraan','Kendaraan\KendaraanController');
Route::get('kendaraan/view/{nopol}','Kendaraan\KendaraanController@nopol');
Route::resource('jeniskendaraan','Kendaraan\JenisKendaraanController');
Route::get('listrso/data/pilihsotersedia/view/{rso}','Rso\ListRsoController@pilihsotersedia');
Route::get('listrso/data/pilihsotidak/view/{rso}','Rso\ListRsoController@pilihsotidak');

Route::resource('bbk','Bbk\BbkController');
Route::resource('listbbk','Bbk\ListBbkController');

Route::resource('listmasuk','Bbm\barangMasukController');

Route::resource('retur','Retur\ReturController');
Route::resource('listretur','Retur\ListReturController');
Route::get('listso/data/lretur/{so}','So\ListSoController@listretur');
Route::get('retur/akses/{groupso}','Retur\ReturController@akses');
Route::get('listretur/bbk/{nomor}/{barang}','Retur\ListReturController@bbk');

Route::resource('user','User\UserController');
Route::get('user/data/all','User\UserController@sales');
Route::get('user/view/{groupso}','User\UserController@groupsales');
Route::get('user/data/purch','User\UserController@purch');

Route::resource('listso','So\ListSoController');
Route::get('listso/data/group','So\ListSoController@group');
Route::get('listso/data/group/{barang}','So\ListSoController@grouplist');
Route::get('listso/data/antrianpo/{barang}','So\ListSoController@antrianpo');
Route::get('listso/data/kembalikanpo/{barang}','So\ListSoController@kembalikanpo');
Route::get('listso/data/bbmbagi/{barang}','So\ListSoController@bbmbagi');

Route::get('listso/data/listbck/{so}','So\ListSoController@listbck');
Route::get('listso/data/{so}/{barang}/{idx}','So\ListSoController@listsobck');

Route::resource('pr','Pr\PrController');

Route::resource('listpo','Po\ListpoController');
Route::get('listpo/data/aktif/{nomor}','Po\ListpoController@sisalist');
Route::get('listpo/data/bcm/{id}','Po\ListpoController@bcm');
Route::get('listpo/data/{po}/{barang}','Po\ListpoController@hapusbck');
Route::get('po/data/laporan','Po\PoController@laporan');


Route::resource('history','History\HistoryController');
Route::get('history/data/{nomorso}/{jenis}','History\HistoryController@historyview');

Route::resource('kategoribarang','Barang\KategoriBarangController');

Route::resource('satuan','Barang\SatuanController');

Route::resource('groupso','Groupso\GroupSoController');
Route::get('groupso/data/aktif','Groupso\GroupSoController@aktif');

Route::resource('formprice','Sales\FormpriceController');
Route::get('formprice/data/{groupso}','Sales\FormpriceController@groupso');
