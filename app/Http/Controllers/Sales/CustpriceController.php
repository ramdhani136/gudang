<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Sales\Custprice;
use App\Http\Resources\CustpriceResource;
use Symfony\Component\HttpFoundation\Response;

class CustpriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CustpriceResource::collection(Custprice::latest()->get());
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
        $data=Custprice::create($request->all());
        return response(new CustpriceResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return  CustpriceResource::collection(Custprice::where('id',$id)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Custprice::where('id',$id)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Custprice::where('id',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function viewprice($customer,$barang){
        return  CustpriceResource::collection(Custprice::where('kode_customer',$customer)->where('kode_barang',$barang)->get());
    }

    public function aktif($customer,$barang){
        return  CustpriceResource::collection(Custprice::where('status','Aktif')->where('kode_customer',$customer)->where('kode_barang',$barang)->get());
    }

    public function tdkaktif($customer,$barang){
        return  CustpriceResource::collection(Custprice::where('status','Non Aktif')->where('kode_customer',$customer)->where('kode_barang',$barang)->get());
    }

    public function tolak($customer,$barang){
        return  CustpriceResource::collection(Custprice::where('status','Di Tolak')->where('kode_customer',$customer)->where('kode_barang',$barang)->get());
    }

    public function ambilPrice($customer,$barang){
        return CustpriceResource::collection(Custprice::where("open","Y")->where('status','Aktif')->where('kode_customer',$customer)->where('kode_barang',$barang)->orderBy('id','DESC')->limit(1)->get());
    }

    public function request()
    {
        return CustpriceResource::collection(Custprice::where('status','Non Aktif')->orderBy('tanggal','ASC')->get());
    }

    public function view($nomor)
    {
        return CustpriceResource::collection(Custprice::where('nomor_price',$nomor)->orderBy('tanggal','ASC')->get());
    }

}
