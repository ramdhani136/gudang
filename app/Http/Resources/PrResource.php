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
            'nomor_pr'=>$this->nomor_pr,
            'tanggal'=>$this->tanggal,
            'id_user'=>$this->id_user,
            'kode_customer'=>$this->kode_customer,
            'alamat'=>$this->customer->alamat,
            'nama_customer'=>$this->customer->nama,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
        ];
    }
}
