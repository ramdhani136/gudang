<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\So\So;

class SoResource extends JsonResource
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
            'nomor_so'=>$this->nomor_so,
            'tanggal_so'=>$this->tanggal_so,
            'tanggal_kirim'=>$this->tanggal_kirim,
            'nomor_rso'=>$this->nomor_rso,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'customer'=>$this->rso->customer->nama,
            'kode_customer'=>$this->rso->customer->kode,
            'nip_sales'=>$this->rso->sales->nip,
            'sales'=>$this->rso->sales->nama,
            'alastolak'=>$this->alastolak,
            'statusso'=>$this->statusso,
            'id_ekspedisi'=>$this->id_ekspedisi,
            'lokasi'=>$this->lokasi,
            'alamat'=>$this->alamat,
            'distribusi'=>$this->distribusi,
        ];
    }
}
