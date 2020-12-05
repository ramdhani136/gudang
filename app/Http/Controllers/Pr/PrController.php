<?php

namespace App\Http\Controllers\Pr;

use App\Http\Controllers\Controller;
use App\Http\Resources\listprResource;
use App\Http\Resources\PrResource;
use App\Model\Pr\Listpr;
use App\Model\Pr\Pr;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PrResource::collection(Pr::orderby('nomor_pr','ASC')->get());
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
        $data= Pr::create($request->all());
        return response(new PrResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PrResource::collection(Pr::where('nomor_pr',$id)->get());
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
        Pr::where('nomor_pr',$id)->update($request->all());
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
        Pr::where('nomor_pr',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }


    public function open()
    {
        return PrResource::collection(Pr::where('status','Sent')->get());
    }
}
