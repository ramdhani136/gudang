<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BckResource extends JsonResource
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
            'bck'=>$this->bck,
            'nomor_so'=>$this->nomor_so,
            'tanggal'=>$this->tanggal,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'customer'=>$this->so->rso->customer->nama,
            'id_kendaran'=>$this->id_kendaraan,
            'nama_kendaraan'=>$this->kendaraan->nama,
            'nopol'=>$this->kendaraan->nopol,
            'kubikasi'=>$this->kendaraan->kubikasi,
            'jeniskendaraan'=>$this->kendaraan->jeniskendaraan->nama
        ];
    }
}
