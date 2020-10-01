<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupSoResource extends JsonResource
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
            'kode'=>$this->kode,
            'area'=>$this->area,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
        ];
    }
}
