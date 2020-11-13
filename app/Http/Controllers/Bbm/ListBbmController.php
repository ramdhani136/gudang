<?php

namespace App\Http\Controllers\Bbm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Bbm\Listbbm;
use App\Http\Resources\ListBbmResource;
use Symfony\Component\HttpFoundation\Response;

class ListBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListBbmResource::collection(Listbbm::all());
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
        $data=Listbbm::create($request->all());
        return response(new ListBbmResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ListBbmResource::collection(Listbbm::where('nomor_bbm',$id)->get());
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
        Listbbm::where('id',$id)->update($request->all());
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
        Listbbm::where('id',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function getbbm(){
        return ListBbmResource::collection(Listbbm::all());
    }

    public function barang($bbm,$barang){
        return ListBbmResource::collection(Listbbm::where('nomor_bbm',$bbm)->where('kode_barang',$barang)->get());
    }
}
