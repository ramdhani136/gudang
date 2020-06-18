<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListRsoResource extends JsonResource
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
            'id_rso'=>$this->id_rso,
            'no_rso'=>$this->rso->nomor_so,
            'id_barang'=>$this->id_barang,
            'nama_barang'=>$this->barang->nama,
            'qty'=>$this->qty,
            'status_stok'=>$this->status_stok,
            'tgl_datang'=>$this->tgl_data,
            'status'=>$this->status,

        ];
    }
}
