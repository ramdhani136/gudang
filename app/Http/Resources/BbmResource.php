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
            'tanggal'=>$this->tanggal,
            'nomor_po'=>$this->nomor_po,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'supplier'=>$this->po->supplier->nama
        ];
    }
}
