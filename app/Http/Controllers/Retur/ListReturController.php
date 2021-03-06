<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Retur\Listretur;
use App\Http\Resources\ListReturResource;
use Symfony\Component\HttpFoundation\Response;

class ListReturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListReturResource::collection(Listretur::all());
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
        $data=Listretur::create($request->all());
        return response(new ListReturResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ListReturResource::collection(Listretur::where('nomor_retur',$id)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return ListReturResource::collection(Listretur::where('id',$id)->get());
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
        Listretur::where('id',$id)->update($request->all());
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
        Listretur::where('id',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function bbk($nomor,$barang)
    {
        return ListReturResource::collection(Listretur::where('nomor_so',$nomor)->where('kode_barang',$barang)->get());
    }
}
