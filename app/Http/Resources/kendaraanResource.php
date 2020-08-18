<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class kendaraanResource extends JsonResource
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
            'id'=>$this->id,
            'nama'=>$this->nama,
            'id_jenis'=>$this->id_jenis,
            'nopol'=>$this->nopol,
            'kubikasi'=>$this->kubikasi,
            'tonase'=>$this->tonase,
            'jenis'=>$this->jeniskendaraan->nama,
            'jenis_keterangan'=>$this->jeniskendaraan->keterangan,
        ];
    }
}
