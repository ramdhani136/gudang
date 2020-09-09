<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListSoResource extends JsonResource
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
            'nomor_rso'=>$this->so->nomor_rso,
            'statusso'=>$this->so->statusso,
            'kode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'harga'=>$this->harga,
            'id_custprice'=>$this->id_custprice,
            'qty'=>$this->qty,
            'catatan'=>$this->catatan,
            'status'=>$this->status,
            'bck'=>$this->bck,
            'bbk'=>$this->bbk,
            'tersedia'=>$this->tersedia,
            'qtyrso'=>$this->qtyrso,
        ];
    }
}
