<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListPoResource extends JsonResource
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
            'nomor_po'=>$this->nomor_po,
            'kode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'harga'=>$this->harga,
            'qty'=>$this->qty,
            'bcm'=>$this->bcm,
            'bbm'=>$this->bbm,
            'closepo'=>$this->closepo,
            'sisapo'=>$this->sisapo,
        ];
    }
}
