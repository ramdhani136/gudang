<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FormpriceResource extends JsonResource
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
            'nomor_price'=>$this->nomor_price,
            'tanggal'=>$this->tanggal,
            'kode_customer'=>$this->kode_customer,
            'customer'=>$this->customer->nama,
            'id_user'=>$this->id_user,
            'user'=>$this->user->name,
            'status'=>$this->status,
            'keterangan'=>$this->keterangan,
            'kode_groupso'=>$this->kode_groupso,
            'salesarea'=>$this->groupso->area,
            'tgl_kordinator'=>$this->tgl_kordinator,
            'tgl_sent'=>$this->tgl_sent,
            'kordinator'=>$this->kordinator,
            'spv'=>$this->spv,
            'tgl_spv'=>$this->tgl_spv,
        ];
    }
}
