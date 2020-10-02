<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RsoResource extends JsonResource
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
            'id_user'=>$this->id_user,
            'namauser'=>$this->user->name,
            'customer'=>$this->customer->nama,
            'alamat'=>$this->customer->alamat,
            'kontak'=>$this->customer->kontak,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'kode_customer'=>$this->kode_customer,
            'pr'=>$this->pr,
            'kode_groupso'=>$this->kode_groupso,
            'areagroup'=>$this->groupso->area,
            'gketerangan'=>$this->groupso->keterangan,
        ];
    }
}
