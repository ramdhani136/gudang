<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturResource extends JsonResource
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
            'nomor_retur'=>$this->nomor_retur,
            'tanggal'=>$this->tanggal,
            'nomor_so'=>$this->nomor_so,
            'status'=>$this->status,
            'keterangan'=>$this->keterangan,
            'customer'=>$this->so->rso->customer->nama,
            'sostatus'=>$this->so->status,
            'statusada'=>$this->so->statusso,
            'alastolak'=>$this->alastolak,
            'kode_groupso'=>$this->kode_groupso,
            'tanggal_so'=>$this->so->tanggal_so,
            'lokasi'=>$this->so->lokasi,
            'alamat'=>$this->so->alamat,
            'distribusi'=>$this->so->distribusi,
            'id_user'=>$this->so->id_user,
            'kode_customer'=>$this->so->rso->kode_customer,
        ];
    }
}
