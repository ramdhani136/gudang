<?php

namespace App\Http\Controllers\So;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListGroupSoResource;
use App\Http\Resources\ListRsoResource;
use Illuminate\Http\Request;
use App\Model\So\Listso;
use App\Http\Resources\ListSoResource;
use App\Http\Resources\SoGroupResource;
use App\Model\Retur\Listretur;
use Symfony\Component\HttpFoundation\Response;

class ListSoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListSoResource::collection(Listso::get());
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
        $data=Listso::create($request->all());
        return response(new ListSoResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ListSoResource::collection(Listso::where('nomor_so',$id)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return ListSoResource::collection(Listso::where('id',$id)->get());
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
        Listso::where('id',$id)->update($request->all());
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
        Listso::where('id',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function  group(){
        $data= Listso::groupBy('kode_barang')->where('statuspo','N')->where('openso','Y')->where('statusso','Tidak Tersedia')
        ->selectRaw('count(*) as total, kode_barang')->get();
        return SoGroupResource::collection($data);
    }

    public function  grouplist($barang){
        return ListGroupSoResource::collection(Listso::where('openso','Y')->where('statusso','Tidak Tersedia')->where('statuspo','N')->where('kode_barang',$barang)->get());
    }

    public function antrianpo($barang){
        return ListSoResource::collection(Listso::where('statuspo','N')->where("openso","Y")->where('kode_barang',$barang)->where('statusso','Tidak Tersedia')->orderBy('pr','ASC')->orderBy('accdate','ASC')->get());
    }

    public function kembalikanpo($barang){
        return ListSoResource::collection(Listso::where('openpo','>',0)->where("openso","Y")->where('kode_barang',$barang)->where('statusso','Tidak Tersedia')->orderBy('pr','DESC')->orderBy('accdate','DESC')->get());
    }

    public function  bbmbagi($barang){
        return  ListSoResource::collection(Listso::where('openso','Y')->where('statusso','Tidak Tersedia')->where('kode_barang',$barang)->orderBy('pr','ASC')->orderBy('accdate','ASC')->get());
    }

    public function  listbck($so){
        return ListSoResource::collection(Listso::where('nomor_so',$so)->where('openso','Y')->where('closeso','N')->get());
    }

    public function  listsobck($so,$barang,$idx){
        return ListSoResource::collection(Listso::where('nomor_so',$so)->where('kode_barang',$barang)->where('idx',$idx)->get());
    }

    public function listretur($so){
        return ListSoResource::collection(Listso::where('nomor_so',$so)->where('bbk','>',0)->get());
    }

}
