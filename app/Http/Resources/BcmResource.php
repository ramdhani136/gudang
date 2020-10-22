<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BcmResource extends JsonResource
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
            'bcm'=>$this->bcm,
            'tanggal'=>$this->tanggal,
            'nomor_po'=>$this->nomor_po,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'supplier'=>$this->po->supplier->nama,
            'nopol'=>$this->nopol,
            'kode_supplier'=>$this->po->kode_supplier,
            'id_user'=>$this->po->id_user,
            'user'=>$this->po->user->name,
        ];
    }
}
