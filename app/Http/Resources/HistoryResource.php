<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'nomor_dok'=>$this->nomor_dok,
            'nomor_ref'=>$this->nomor_ref,
            'id_user'=>$this->id_user,
            'user'=>$this->user->name,
            'notif'=>$this->notif,
            'keterangan'=>$this->keterangan,
            'jenis'=>$this->jenis,
            'tanggal'=>$this->tanggal,
            'aktif'=>$this->aktif,
        ];
    }
}
