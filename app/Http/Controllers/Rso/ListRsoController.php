<?php

namespace App\Http\Controllers\Rso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Rso\Listrso;
use App\Http\Resources\ListRsoResource;
use App\Http\Resources\GrouplistResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\listpoPriceResource;



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
        return  ListRsoResource::collection(Listrso::where('nomor_rso',$no)->where('acc_purch','Y')->where('qty_tdktersedia','>',0)->get());
    }

    public function dicacc($no){
        return  ListRsoResource::collection(Listrso::where('nomor_rso',$no)->where('qty_tersedia','>',0)->get());
    }

    public function  group(){
        $data= Listrso::where('qty_tdktersedia','>',0)->where('so_open','Y')->groupBy('kode_barang')->where('open_po','N')
        ->selectRaw('count(*) as total, kode_barang')->get();
        return GrouplistResource::collection($data);
    }

    
    public function  grouplist($barang){
        return ListRsoResource::collection(Listrso::where('qty_tdktersedia','>',0)->where('so_open','Y')->where('kode_barang',$barang)->where('open_po','N')->get());
    }

    public function sOpen(Request $request, $no){
        Listrso::where('nomor_rso',$no)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    public function  listpo($po){
        $data= Listrso::where('nomor_po',$po)->where('qty_tdktersedia','>',0)->where('so_open','Y')->groupBy('kode_barang')->groupBy('nomor_po')->groupBy('harga_supplier')->where('open_po','Y')
        ->selectRaw('count(*) as total, kode_barang')->selectRaw('count(*) as total, nomor_po')->selectRaw('count(*) as total, harga_supplier')->get();
        return GrouplistResource::collection($data);
    }


}
