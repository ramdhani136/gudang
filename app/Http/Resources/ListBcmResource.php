<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListBcmResource extends JsonResource
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
            'id'=>$this->id,
            'nomor_bcm'=>$this->nomor_bcm,
            'kode_barang'=>$this->kode_barang,
            'qty'=>$this->qty,
            'keterangan'=>$this->keterangan,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
        ];
    }
}
