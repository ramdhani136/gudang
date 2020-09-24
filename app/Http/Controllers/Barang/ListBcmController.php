<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Barang\Listbcm;
use App\Http\Resources\ListBcmResource;
use Symfony\Component\HttpFoundation\Response;

class ListBcmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListBcmResource::collection(Listbcm::latest()->get());
    }

    public function count($bcm,$barang)
    {
        return ListBcmResource::collection(Listbcm::where('nomor_bcm',$bcm)->where('kode_barang',$barang)->get());
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
        $data=Listbcm::create($request->all());
        return response(new ListBcmResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($listbcm)
    {
        return ListBcmResource::collection(Listbcm::where('nomor_bcm',$listbcm)->get());
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
    public function update(Request $request, $listbcm)
    {
        Listbcm::where('id',$listbcm)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($listbcm)
    {
        Listbcm::where('id',$listbcm)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function nnonaktif($bcm)
    {
        return ListBcmResource::collection(Listbcm::where('close_bcm','N')->where('nomor_bcm',$bcm)->latest()->get());
    }

    public function getbbm($bcm,$barang){
        return ListBcmResource::collection(Listbcm::where('kode_barang',$barang)->where('nomor_bcm',$bcm)->latest()->get());
    }

    
}
