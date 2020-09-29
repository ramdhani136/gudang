<?php

namespace App\Http\Controllers\Bck;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Bck\Listbck;
use App\Http\Resources\ListBckResource;
use Symfony\Component\HttpFoundation\Response;

class ListBckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListBckResource::collection(Listbck::all());
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
        $data=Listbck::create($request->all());
        return response(new ListBckResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ListBckResource::collection(Listbck::where('nomor_bck',$id)->get());
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
        Listbck::where('id',$id)->update($request->all());
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
        Listbck::where('id',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function aktif($bck){
        return ListBckResource::collection(Listbck::where('close_bck','N')->where('nomor_bck',$bck)->get());
    }

    public function viewbbk($bck,$barang){
        return ListBckResource::collection(Listbck::where('nomor_bck',$bck)->where('kode_barang',$barang)->get());
    }
}
