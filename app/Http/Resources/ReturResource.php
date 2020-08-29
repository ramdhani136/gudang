<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturResource extends JsonResource
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
            'nomor_retur'=>$this->nomor_retur,
            'tanggal'=>$this->tanggal,
            'nomor_so'=>$this->nomor_so,
            'status'=>$this->status,
            'keterangan'=>$this->keterangan,
            'customer'=>$this->so->rso->customer->nama,
            'sostatus'=>$this->so->status,
            'statusada'=>$this->so->statusso,
            'alastolak'=>$this->alastolak,
        ];
    }
}
