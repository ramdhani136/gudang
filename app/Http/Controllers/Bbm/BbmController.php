<?php

namespace App\Http\Controllers\Bbm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Bbm\Bbm;
use App\Http\Resources\BbmResource;
use App\Model\Bbm\Listbbm;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade as PDF;

class BbmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BbmResource::collection(Bbm::orderBy('updated_at','ASC')->get());
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
        $data=Bbm::create($request->all());
        return response(new BbmResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BbmResource::collection(Bbm::where('bbm',$id)->get());
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
        Bbm::where('bbm',$id)->update($request->all());
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
        Bbm::where('bbm',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function print($bbm){
        $getbbm=Bbm::where('bbm',$bbm)->get();    
        $getlist=Listbbm::where('nomor_bbm',$bbm)->get();
        // $pdf = view('print.bbm',['bbm'=>$getbbm,'list'=>$getlist]);
        $pdf = PDF::loadview('print.bbm',['bbm'=>$getbbm,'list'=>$getlist])->setPaper([0, 0, 396.8, 585.98], 'landscape');
        // return $pdf;
        return   $pdf->stream($bbm);
    }
}
