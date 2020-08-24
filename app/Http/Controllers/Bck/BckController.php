<?php

namespace App\Http\Controllers\Bck;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Bck\Bck;
use App\Http\Resources\BckResource;
use Symfony\Component\HttpFoundation\Response;

class BckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BckResource::collection(Bck::orderBy('updated_at','ASC')->get());
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
        $data=Bck::create($request->all());
        return response(new BckResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BckResource::collection(Bck::where('bck',$id)->get());
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
        Bck::where('bck',$id)->update($request->all());
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
        Bck::where('bck',$id)->delete();
        return response('deleted',response::HTTP_OK);
    }

    public function aktif()
    {
        return BckResource::collection(Bck::where('status','open')->orderBy('bck','ASC')->get());
    }
}
