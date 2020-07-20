<?php

namespace App\Http\Controllers\Rso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Rso\Listrso;
use App\Http\Resources\ListRsoResource;
use Symfony\Component\HttpFoundation\Response;

class ListRsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListRsoResource::collection(Listrso::get());
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
        $data=Listrso::create($request->all());
        return response(new ListRsoResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($listrso)
    {
        return ListRsoResource::collection(Listrso::where('nomor_rso',$listrso)->latest()->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($listrso)
    {
        return ListRsoResource::collection(Listrso::where('id',$listrso)->latest()->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$listrso)
    {
        Listrso::where('id',$listrso)->update($request->all());
        return response('update',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($listrso)
    {
        Listrso::where('id',$listrso)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function purch($id){
        return ListRsoResource::collection(Listrso::where('nomor_rso',$id)->where('qty_tdktersedia', '>', 0)->get());
    }

}
