<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListBckResource extends JsonResource
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
            'nomor_bck'=>$this->nomor_bck,
            'kode_barang'=>$this->kode_barang,
            'qty'=>$this->qty,
            'keterangan'=>$this->keterangan,
            'close_bck'=>$this->close_bck,
            'bck'=>$this->bck->nomor_so,
            'nomor_rso'=>$this->bck->so->nomor_rso,
            'statusso'=>$this->bck->so->statusso,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'harga'=>$this->harga,
            'diskon'=>$this->diskon,
            'nomor_so'=>$this->bck->nomor_so,
            'idx'=>$this->idx,
        ];
    }
}
