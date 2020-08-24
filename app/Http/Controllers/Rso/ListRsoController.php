<?php

namespace App\Http\Controllers\Rso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Rso\Listrso;
use App\Http\Resources\ListRsoResource;
use App\Http\Resources\GrouplistResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\listpoPriceResource;
use App\Http\Resources\GroupItemResource;
use App\Http\Resources\listGroupResource;


class ListRsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListRsoResource::collection(Listrso::get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=Listrso::create($request->all());
        return response(new ListRsoResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($listrso)
    {
        return ListRsoResource::collection(Listrso::where('nomor_rso',$listrso)->latest()->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($listrso)
    {
        return ListRsoResource::collection(Listrso::where('id',$listrso)->latest()->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$listrso)
    {
        Listrso::where('id',$listrso)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($listrso)
    {
        Listrso::where('id',$listrso)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function purch($id){
        return ListRsoResource::collection(Listrso::where('nomor_rso',$id)->where('qty_tdktersedia', '>', 0)->get());
    }

    public function booking(Request $request, $no){
        Listrso::where('nomor_rso',$no)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    public function purchacc($no){
        return  ListRsoResource::collection(Listrso::where('so_tdktersedia','N')->where('nomor_rso',$no)->where('acc_purch','Y')->where('qty_tdktersedia','>',0)->get());
    }

    public function dicacc($no){
        return  ListRsoResource::collection(Listrso::where('so_tersedia','N')->where('nomor_rso',$no)->where('qty_tersedia','>',0)->get());
    }

    public function sotersedia($no){
        return  ListRsoResource::collection(Listrso::where('nomor_rso',$no)->where('qty_tersedia','>',0)->get());
    }

    public function sott($no){
        return  ListRsoResource::collection(Listrso::where('nomor_rso',$no)->where('acc_purch','Y')->where('qty_tdktersedia','>',0)->get());
    }

    public function  group(){
        $data= Listrso::where('qty_tdktersedia','>',0)->where('so_open','Y')->groupBy('kode_barang')->where('open_po','N')
        ->selectRaw('count(*) as total, kode_barang')->get();
        return GrouplistResource::collection($data);
    }

    
    public function  grouplist($barang){
        return listGroupResource::collection(Listrso::where('statusso','Y')->where('qty_tdktersedia','>',0)->where('so_tdktersedia','Y')->where('kode_barang',$barang)->where('open_po','N')->get());
    }

    public function  groupopen($po,$barang){
        return listGroupResource::collection(Listrso::where('nomor_po',$po)->where('qty_tdktersedia','>',0)->where('so_open','Y')->where('kode_barang',$barang)->where('open_po','Y')->get());
    }

    public function sOpen(Request $request, $no){
        Listrso::where('nomor_rso',$no)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    public function  listpo($po){
        $data= Listrso::where('nomor_po',$po)->where('qty_tdktersedia','>',0)->where('so_open','Y')->groupBy('kode_barang')->groupBy('nomor_po')->groupBy('harga_supplier')->where('open_po','Y')
        ->selectRaw('count(*) as total, kode_barang')->selectRaw('count(*) as total, nomor_po')->selectRaw('count(*) as total, harga_supplier')->orderBy('dateaccso','ASC')->get();
        return GroupItemResource::collection($data);
    }

    public function  pilihpo($po){
        $data= Listrso::where('po_close','N')->where('nomor_po',$po)->where('qty_tdktersedia','>',0)->where('so_open','Y')->groupBy('kode_barang')->groupBy('nomor_po')->groupBy('harga_supplier')->where('open_po','Y')
        ->selectRaw('count(*) as total, kode_barang')->selectRaw('count(*) as total, nomor_po')->selectRaw('count(*) as total, harga_supplier')->orderBy('dateaccso','ASC')->get();
        return GroupItemResource::collection($data);
    }

    public function  listclose($po){
        $data= Listrso::where('nomor_po',$po)->where('qty_tdktersedia','>',0)->where('so_open','Y')->where('po_close','Y')->groupBy('kode_barang')->groupBy('nomor_po')->groupBy('harga_supplier')->where('open_po','Y')
        ->selectRaw('count(*) as total, kode_barang')->selectRaw('count(*) as total, nomor_po')->selectRaw('count(*) as total, harga_supplier')->get();
        return GroupItemResource::collection($data);
    }

    public function  listall($po){
        $data= Listrso::where('nomor_po',$po)->where('qty_tdktersedia','>',0)->where('so_open','Y')->groupBy('kode_barang')->groupBy('nomor_po')->groupBy('harga_supplier')->where('open_po','Y')
        ->selectRaw('count(*) as total, kode_barang')->selectRaw('count(*) as total, nomor_po')->selectRaw('count(*) as total, harga_supplier')->get();
        return GroupItemResource::collection($data);
    }

    public function deletePo(Request $request, $po){
        Listrso::where('nomor_po',$po)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    public function statuspo(Request $request, $po,$barang){
        Listrso::where('nomor_po',$po)->where('kode_barang',$barang)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    public function detailpo($po,$barang){
        $data=Listrso::where('nomor_po',$po)->where('kode_barang',$barang)->orderBy('dateaccso','ASC')->get();
        return  ListRsoResource::collection($data);
    }

    public function accpo($po,$barang){
        $data=Listrso::where('nomor_po',$po)->where('kode_barang',$barang)->orderBy('dateaccso','DESC')->get();
        return  ListRsoResource::collection($data);
    }

    public function  pilihsotersedia($rso){
        $data= Listrso::where('sotersedia_close','N')->where('nomor_rso',$rso)->where('so_open','Y')->where('qty_tersedia','>',0)->get();
        return  ListRsoResource::collection($data);
    }

    public function  pilihsotidak($rso){
        $data= Listrso::where('sotdk_close','N')->where('nomor_rso',$rso)->where('so_open','Y')->where('qty_tdktersedia','>',0)->get();
        return  ListRsoResource::collection($data);
    }

    public function  listcheckermasuk($rso,$barang){
        $data= Listrso::where('nomor_rso',$rso)->where('kode_barang',$barang)->get();
        return  ListRsoResource::collection($data);
    }

    public function viewlistso($rso,$barang){
        return ListRsoResource::collection(Listrso::where('nomor_rso',$rso)->where('kode_barang',$barang)->get());
    }

}
