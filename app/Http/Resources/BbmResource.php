<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BbmResource extends JsonResource
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
            'bbm'=>$this->bbm,
            'nomor_bcm'=>$this->nomor_bcm,
            'tanggal'=>$this->tanggal,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'nomor_po'=>$this->bcm->nomor_po,
            'nopol'=>$this->bcm->nopol,
            'supplier'=>$this->bcm->po->supplier->nama
        ];
    }
}
