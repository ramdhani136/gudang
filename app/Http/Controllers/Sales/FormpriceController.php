<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormpriceResource;
use App\Model\Sales\Custprice;
use App\Sales\Formprice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade as PDF;

class FormpriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FormpriceResource::collection(Formprice::orderBy('updated_at','ASC')->get());
    }

    public function groupso($groupso)
    {
        return FormpriceResource::collection(Formprice::where('kode_groupso',$groupso)->orderBy('updated_at','ASC')->get());
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
        $data=Formprice::create($request->all());
        return response(new FormpriceResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return FormpriceResource::collection(Formprice::where('nomor_price',$id)->get());
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
        Formprice::where('nomor_price',$id)->update($request->all());
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
        Formprice::where('nomor_price',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function print($nomor){
        $getform=Formprice::where('nomor_price',$nomor)->get();    
        $getcustprice=Custprice::where('nomor_price',$nomor)->where('status','Aktif')->get();
        // $count=0;
        // $harga=array();
        // foreach($getcustprice as $a){
        // $data= Custprice::where("open","Y")->where('status','Aktif')->where('kode_customer',$a->kode_customer)->where('kode_barang',$a->kode_barang)->orderBy('id','DESC')->limit(1)->get();
        // if($data[0]->harga<1){
        //     $data[0]->harga=0;
        // }
        // $harga[$count++]=array(
        //     'harga'=>$data[0]->harga,
        // );
        // $ambilharga=json_encode($harga);
        // }
        // return $getform
   

        // $pdf = view('print.formprice',['form'=>$getform,'list'=>$getcustprice]);
        $pdf = PDF::loadview('print.formprice',['form'=>$getform,'list'=>$getcustprice])->setPaper([0, 0, 609.45, 396.85], 'potrait');
        // return $pdf;
        return   $pdf->stream($nomor);
    }
}
