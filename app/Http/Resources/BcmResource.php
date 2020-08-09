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
            'supplier'=>$this->po->supplier->nama
        ];
    }
}
