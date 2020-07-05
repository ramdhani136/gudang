<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
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
            'current_page'=>$this->current_page,
            'id'=>$this->id,
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'qty'=>$this->qty,
            'satuan'=>$this->satuan,
        ];
    }
}
