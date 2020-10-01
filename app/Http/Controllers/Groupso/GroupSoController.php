<?php

namespace App\Http\Controllers\Groupso;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupSoResource;
use App\Model\Groupso\Groupso;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class GroupSoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return GroupSoResource::collection(Groupso::orderBy('area','ASC')->get());
    }

    public function aktif(){
        return GroupSoResource::collection(Groupso::orderBy('area','ASC')->where('status','Aktif')->get());
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
        $data=Groupso::create($request->all());
        return response(new GroupSoResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return GroupSoResource::collection(Groupso::where('kode',$id)->get());
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
        Groupso::where('kode',$id)->update($request->all());
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
        Groupso::where('kode',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }
}
