<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListBbmResource extends JsonResource
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
            'id'=>$this->id,
            'nomor_bbm'=>$this->nomor_bbm,
            'qty'=>$this->qty,
            'keterangan'=>$this->keterangan,
        ];
    }
}
