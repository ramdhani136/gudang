<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListGroupSoResource extends JsonResource
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
            'nomor_so'=>$this->nomor_so,
            'jumlah'=>($this->qty)-($this->openpo),
            'customer'=>$this->so->rso->customer->nama,
            'satuan'=>$this->barang->satuan,
            'kode'=>$this->barang->kode,
            'nama_barang'=>$this->barang->nama,
        ];
    }
}
