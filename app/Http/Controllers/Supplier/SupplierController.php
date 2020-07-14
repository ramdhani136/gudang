<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Supplier\Supplier;
use App\Http\Resources\SupplierResource;
use Symfony\Component\HttpFoundation\Response;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  SupplierResource::collection(Supplier::latest()->get());
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
        $this->validate($request,[
            'kode_supplier'=>'required',
            'nama'=>'required',
        ]);

        $data=Supplier::create($request->all());
        return response(new SupplierResource($data),response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($supplier)
    {
        return SupplierResource::collection(Supplier::where('kode_supplier',$supplier)->get());
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
    public function update(Request $request, $supplier)
    {
        Supplier::where('kode_supplier',$supplier)->update([
            'kode_supplier'=>$request->kode_supplier,
            'nama'=>$request->nama,
            'alamat'=>$request->alamat,
            'pic'=>$request->pic,
            'telepon'=>$request->telepon,
            'email'=>$request->email,
            'status'=>$request->status,
        ]);
        
        return response('update',response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($supplier)
    {
        Supplier::where('kode_supplier',$supplier)->delete();
    }
}
