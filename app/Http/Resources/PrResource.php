<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrResource extends JsonResource
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
            'nomor_rso'=>$this->nomor_rso,
            'tanggal_rso'=>$this->tanggal_rso,
            'nip_sales'=>$this->nip_sales,
            'customer'=>$this->customer->nama,
            'alamat'=>$this->customer->alamat,
            'kontak'=>$this->customer->kontak,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'kode_customer'=>$this->kode_customer,
        ];
    }
}
