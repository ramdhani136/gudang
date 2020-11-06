<?php

namespace App\Http\Controllers\Po;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Po\Po;
use App\Http\Resources\PoResource;
use App\Model\Po\Listpo;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade as PDF;

class PoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PoResource::collection(Po::orderBy('nomor_po','ASC')->get());
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
        $data=Po::create($request->all());
        return response(new PoResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($po)
    {
        return PoResource::collection(Po::where('nomor_po',$po)->get());
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
    public function update(Request $request, $po)
    {
        Po::where('nomor_po',$po)->update($request->all());
        return response('updated',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($po)
    {
        Po::where('nomor_po',$po)->delete();
    }

    public function aktif()
    {
        return PoResource::collection(Po::orderBy('nomor_po','ASC')->where('status','Acc')->where('poselesai','N')->get());
    }

    public function rselesai()
    {
        return PoResource::collection(Po::orderBy('updated_at','ASC')->where('status','Acc')->where('rs','Y')->get());
    }

    public function print($po){
        $getpo=Po::where('nomor_po',$po)->get();    
        $getlist=Listpo::where('nomor_po',$po)->get();
        // $pdf = view('print.so',['so'=>$getso,'listso'=>$getlistso]);
        $pdf = PDF::loadview('print.po',['po'=>$getpo,'list'=>$getlist])->setPaper([0, 0, 684, 396], 'potrait');
        // return $pdf;
        return   $pdf->stream($po);
    }

    public function laporan()
    {
        return PoResource::collection(Po::where('status','Acc')->orWhere('status','Selesai')->orWhere('status','Di Selesaikan')->orderBy('tanggal_po','ASC')->get());
    }

}
