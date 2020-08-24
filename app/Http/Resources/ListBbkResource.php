<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListBbkResource extends JsonResource
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
            'nomor_bbk'=>$this->nomor_bbk,
            'nomor_bck'=>$this->bbk->nomor_bck,
            'nomor_so'=>$this->bbk->bck->so->nomor_so,
            'kode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'qty'=>$this->qty,
            'harga'=>$this->harga,
            'qty_bck'=>$this->qty_bck,
            'keterangan'=>$this->keterangan,
            'close_bbk'=>$this->close_bbk
        ];
    }
}
