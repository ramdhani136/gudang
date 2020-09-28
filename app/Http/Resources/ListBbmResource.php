<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListBbmResource extends JsonResource
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
            'nomor_bbm'=>$this->nomor_bbm,
            'kode_barang'=>$this->kode_barang,
            'qty'=>$this->qty,
            'harga'=>$this->harga,
            'diskon'=>$this->diskon,
            'keterangan'=>$this->keterangan,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'bcm'=>$this->bbm->nomor_bcm,
        ];
    }
}
