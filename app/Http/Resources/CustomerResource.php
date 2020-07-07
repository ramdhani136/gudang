<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class CustomerResource extends JsonResource
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
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'keterangan'=>$this->keterangan,
        ];
    }
}
