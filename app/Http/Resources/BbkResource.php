<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BbkResource extends JsonResource
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
            'bbk'=>$this->bbk,
            'nomor_bck'=>$this->nomor_bck,
            'tanggal'=>$this->tanggal,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'customer'=>$this->bck->so->rso->customer->nama,
            'nomor_rso'=>$this->bck->so->nomor_rso,
            'lokasi'=>$this->bck->so->lokasi,
            'alamat'=>$this->bck->so->alamat,
            'dsitribusi'=>$this->bck->so->distribusi,
            'statusso'=>$this->bck->so->statusso,
            'id_ekspedisi'=>$this->bck->so->id_ekspedisi,
            'nomor_so'=>$this->bck->nomor_so
        ];
    }
}
