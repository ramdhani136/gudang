<?php

namespace App\Http\Controllers\Pr;


use App\Http\Controllers\Controller;
use App\Http\Resources\lgrouppr;
use App\Http\Resources\ListprGroupResource;
use Illuminate\Http\Request;
use App\Http\Resources\listprResource;
use App\Model\Pr\Listpr;
use Symfony\Component\HttpFoundation\Response;

class ListprController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return listprResource::collection(Listpr::orderby('nomor_pr','ASC')->get());
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
        $data= Listpr::create($request->all());
        return response(new listprResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return listprResource::collection(Listpr::where('nomor_pr',$id)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function id($id)
    {
        return listprResource::collection(Listpr::where('id',$id)->get());
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
        Listpr::where('id',$id)->update($request->all());
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
        Listpr::where('id',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function  group(){
        $data= Listpr::groupBy('kode_barang')->where('close','N')->where('status','Y')
        ->selectRaw('count(*) as total, kode_barang')->get();
        return ListprGroupResource::collection($data);
    }

    public function  grouplist($barang){
        return lgrouppr::collection(Listpr::where('status','Y')->where('close','N')->where('kode_barang',$barang)->get());
    }

    public function antrianpr($barang){
        return listprResource::collection(Listpr::where('status','Y')->where("close","N")->where('kode_barang',$barang)->orderBy('accdate','ASC')->get());
    }

    public function kembalikanpr($barang){
        return listprResource::collection(Listpr::where('status','Y')->where('kode_barang',$barang)->orderBy('accdate','DESC')->get());
    }

}
