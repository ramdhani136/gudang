<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListReturResource extends JsonResource
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
            'nomor_retur'=>$this->nomor_retur,
            'kode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'qty'=>$this->qty,
            'real'=>$this->real,
            'catatan'=>$this->catatan,
            'nomor_so'=>$this->nomor_so,
        ];
    }
}
