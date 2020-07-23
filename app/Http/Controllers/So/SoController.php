<?php

namespace App\Http\Controllers\So;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\So\So;
use App\Http\Resources\SoResource;
use Symfony\Component\HttpFoundation\Response;

class SoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SoResource::collection(So::orderBy('updated_at','ASC')->get());
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
        $data=So::create($request->all());
        return response(new SoResource($data),response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return SoResource::collection(So::Where('nomor_so',$id)->get()); 
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
    public function update(Request $request,$id)
    {
        So::where('nomor_so',$id)->update($request->all());
        return response('updated',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        So::where('nomor_so',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }
}
