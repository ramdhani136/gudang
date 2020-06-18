<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RsoResource extends JsonResource
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
            'tanggal_rso'=>$this->tanggal_rso,
            'user'=>$this->user->name,
            'customer'=>$this->customer->nama,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
        ];
    }
}
