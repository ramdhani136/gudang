<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return 
        [
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'alamat'=>$this->alamat,
            'kontak'=>$this->kontak,
            'status'=>$this->status,
            'nip_sales'=>$this->nip_sales,
            'keterangan'=>$this->keterangan,
        ];
    }
}
