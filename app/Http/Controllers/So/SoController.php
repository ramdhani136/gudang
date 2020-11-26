<?php

namespace App\Http\Controllers\So;

use App\Http\Controllers\Controller;
use App\Http\Resources\SoResource;
use App\Model\So\Listso;
use App\Model\So\So;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;





use Symfony\Component\HttpFoundation\Response;

class SoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SoResource::collection(So::orderBy('updated_at','ASC')->get());
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
        $data=So::create($request->all());
        return response(new SoResource($data),response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return SoResource::collection(So::Where('nomor_so',$id)->get()); 
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
    public function update(Request $request,$id)
    {
        So::where('nomor_so',$id)->update($request->all());
        return response('updated',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        So::where('nomor_so',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function aktif()
    {
        return SoResource::collection(So::where('status','Acc')->where('pr','N')->orderBy('nomor_so','ASC')->where('closebck','N')->get());
    }

    public function realso()
    {
        return SoResource::collection(So::where('pr','N')->orderBy('nomor_so','ASC')->get());
    }
    
    public function skordinator($groupso)
    {
        return SoResource::collection(So::where('status','Acc')->where('rs','Y')->where('rss','N')->where('kode_groupso',$groupso)->orderBy('nomor_so','ASC')->get());
    }

    public function ssupervisor()
    {
        return SoResource::collection(So::where('status','Acc')->where('rs','Y')->where('rss','Y')->orderBy('nomor_so','ASC')->get());
    }

    public function statusopen($rso)
    {
        return SoResource::collection(So::Where('nomor_rso',$rso)->get()); 
    }

    public function groupso($groupso)
    {
        return SoResource::collection(So::where('kode_groupso',$groupso)->where('pr','N')->orderBy('nomor_so','ASC')->get());
    }

    public function print($so){
        $getso=So::where('nomor_so',$so)->get();    
        $getlistso=Listso::where('nomor_so',$so)->get();
        // $pdf = view('print.so',['so'=>$getso,'listso'=>$getlistso]);
        $pdf = PDF::loadview('print.so',['so'=>$getso,'listso'=>$getlistso])->setPaper([0, 0,609.45, 396.85], 'potrait');
        // return $pdf;
        return   $pdf->stream($so);
    }

    public function laporan()
    {
        return SoResource::collection(So::where('pr','N')->where('status','Acc')->orWhere('status','Selesai')->orWhere('status','Di Selesaikan')->orderBy('tanggal_so','ASC')->get());
    }

//     public function sales($groupso,$sales)
//     {
//         return SoResource::collection(So::where('pr','N')->where('kode_groupso',$groupso)->where('id_user',$sales)->orderBy('nomor_so','ASC')->get());
//     }
}
