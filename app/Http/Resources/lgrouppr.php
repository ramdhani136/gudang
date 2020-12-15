<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class lgrouppr extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'id'=>$this->id,
            'nomor_pr'=>$this->nomor_pr,
            'jumlah'=>($this->qty)-($this->po),
            'satuan'=>$this->barang->satuan,
            'kode'=>$this->barang->kode,
            'nama_barang'=>$this->barang->nama,
        ];
    }
}
