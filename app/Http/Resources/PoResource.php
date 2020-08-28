<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PoResource extends JsonResource
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
            'nomor_po'=>$this->nomor_po,
            'tanggal_po'=>$this->tanggal_po,
            'kode_supplier'=>$this->kode_supplier,
            'supplier'=>$this->supplier->nama,
            'tanggal_datang'=>$this->tanggal_datang,
            'keterangan'=>$this->keterangan,
            'nip_purchasing'=>$this->nip_purchasing,
            'purchasing'=>$this->purchasing->nama,
            'status'=>$this->status,
            'keterangan'=>$this->keterangan,
            'alastolak'=>$this->alastolak,
            'rs'=>$this->rs,
            'alasselesai'=>$this->alasselesai,
            'alastolakrequest'=>$this->alastolakrequest,
        ];
    }
}
