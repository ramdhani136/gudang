<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'kode_supplier'=>$this->kode_supplier,
            'nama'=>$this->nama,
            'alamat'=>$this->alamat,
            'pic'=>$this->pic,
            'telepon'=>$this->telepon,
            'email'=>$this->email,
            'status'=>$this->status,
        ];
    }
}
