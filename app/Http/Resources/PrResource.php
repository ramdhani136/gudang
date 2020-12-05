<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrResource extends JsonResource
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
            'nomor_pr'=>$this->nomor_pr,
            'tanggal'=>$this->tanggal,
            'id_user'=>$this->id_user,
            'user'=>$this->user->name,
            'keterangan'=>$this->keterangan,
            'tgl_inventory'=>$this->tgl_inventory,
            'purchase'=>$this->purchase,
            'tgl_purchase'=>$this->tgl_purchase,
            'status'=>$this->status,
        ];
    }
}
