<?php

namespace App\Http\Controllers\Rso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Rso\Rso;
use App\Http\Resources\RsoResource;
use App\Http\Resources\PrResource;
use App\Model\Rso\Listrso;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade as PDF;

class RsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RsoResource::collection(Rso::orderBy('updated_at','ASC')->get());
    }

    public function groupso($groupso)
    {
        return RsoResource::collection(Rso::where('kode_groupso',$groupso)->where('pr','N')->orderBy('nomor_rso','ASC')->get());
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
        $data=Rso::create($request->all());
        return response(new RsoResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($rso)
    {
        return RsoResource::collection(Rso::where('nomor_rso',$rso)->get());
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
    public function update(Request $request,$rso)
    {
        Rso::where('nomor_rso',$rso)->update($request->all());
        return response('updated',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($rso)
    {
        Rso::where('nomor_rso',$rso)->delete();
    }

    public function purch(){
        return RsoResource::collection(Rso::where('status','Purch')->orderBy('updated_at','ASC')->get());
    }

    public function confirm(){
        return RsoResource::collection(Rso::where('pr','N')->where('status','Confirmed')->orderBy('updated_at','ASC')->get());
    }

    public function confirmgroup($groupso){
        return RsoResource::collection(Rso::where('kode_groupso',$groupso)->where('pr','N')->where('status','Confirmed')->orderBy('updated_at','ASC')->get());
    }

    public function pr()
    {
        return PrResource::collection(Rso::where('pr','Y')->orderBy('nomor_rso','ASC')->get());
    }

    public function viewpr($rso)
    {
        return PrResource::collection(Rso::where('nomor_rso',$rso)->where('pr','Y')->orderBy('updated_at','ASC')->get());
    }

    public function realrso()
    {
        return RsoResource::collection(Rso::where('pr','N')->orderBy('nomor_rso','ASC')->get());
    }

    public function printtersedia($rso){
        $getrso=Rso::where('nomor_rso',$rso)->get();    
        $getlist=Listrso::where('nomor_rso',$rso)->where('status','Tersedia')->get();
        // $pdf = view('print.so',['so'=>$getso,'listso'=>$getlistso]);
        $pdf = PDF::loadview('print.rsotersedia',['rso'=>$getrso,'list'=>$getlist])->setPaper([0, 0, 609.45, 396.85], 'potrait');
        // return $pdf;
        return   $pdf->stream($rso);
    }

    public function printtdk($rso){
        $getrso=Rso::where('nomor_rso',$rso)->get();    
        $getlist=Listrso::where('nomor_rso',$rso)->where('status','Tidak Tersedia')->where('acc_purch','Y')->get();
        // $pdf = view('print.so',['so'=>$getso,'listso'=>$getlistso]);
        $pdf = PDF::loadview('print.rsott',['rso'=>$getrso,'list'=>$getlist])->setPaper([0, 0,  609.45, 396.85], 'potrait');
        // return $pdf;
        return   $pdf->stream($rso);
    }

}
