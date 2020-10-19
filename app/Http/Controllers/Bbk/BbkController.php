<?php

namespace App\Http\Controllers\Bbk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Bbk\Bbk;
use App\Http\Resources\BbkResource;
use App\Model\Bbk\Listbbk;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade as PDF;

class BbkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BbkResource::collection(Bbk::orderBy('bbk','ASC')->get());
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
        $data=Bbk::create($request->all());
        return response(new BbkResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BbkResource::collection(Bbk::where('bbk',$id)->get());
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
        Bbk::where('bbk',$id)->update($request->all());
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
        Bbk::where('bbk',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function print($bbk){
        $getbbk=Bbk::where('bbk',$bbk)->get();    
        $getlist=Listbbk::where('nomor_bbk',$bbk)->get();
        // $pdf = view('print.bbk',['bbk'=>$getbbk,'list'=>$getlist]);
        $pdf = PDF::loadview('print.bbk',['bbk'=>$getbbk,'list'=>$getlist])->setPaper([0, 0, 396.8, 585.98], 'landscape');
        // return $pdf;
        return   $pdf->stream($bbk);
    }

}
