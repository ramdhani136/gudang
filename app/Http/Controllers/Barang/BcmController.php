<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Barang\Bcm;
use App\Http\Resources\BcmResource;
use App\Model\Barang\Listbcm;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade as PDF;

class BcmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BcmResource::collection(Bcm::orderBy('bcm','ASC')->get());
    }


    public function open()
    {
        return BcmResource::collection(Bcm::where('status',"open")->orderBy('bcm','ASC')->get());
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
        $data=Bcm::create($request->all());
        return response(new BcmResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BcmResource::collection(Bcm::where('bcm',$id)->get());
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
        Bcm::where('bcm',$id)->update($request->all());
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
        Bcm::where('bcm',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function print($bcm){
        $getbcm=Bcm::where('bcm',$bcm)->get();    
        $getlist=Listbcm::where('nomor_bcm',$bcm)->get();
        // $pdf = view('print.bcm',['bcm'=>$getbcm,'list'=>$getlist]);
        $pdf = PDF::loadview('print.bcm',['bcm'=>$getbcm,'list'=>$getlist])->setPaper('A4', 'landscape');
        // return $pdf;
        return   $pdf->stream($bcm);
    }
}
