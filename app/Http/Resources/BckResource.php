<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BckResource extends JsonResource
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
            'bck'=>$this->bck,
            'nomor_so'=>$this->nomor_so,
            'tanggal'=>$this->tanggal,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'kode_customer'=>$this->so->rso->customer->kode,
            'customer'=>$this->so->rso->customer->nama,
            'id_kendaraan'=>$this->id_kendaraan,
            'distribusi'=>$this->so->distribusi,
            'lokasi'=>$this->so->lokasi,
            'alamat'=>$this->so->alamat,
            'id_ekspedisi'=>$this->so->id_ekspedisi,
            'id_user'=>$this->so->id_user,
            'kode_groupso'=>$this->so->kode_groupso,
            'sales'=>$this->so->user->name,
     /*        'nama_kendaraan'=>$this->kendaraan->nama,
            'nopol'=>$this->kendaraan->nopol,
            'kubikasi'=>$this->kendaraan->kubikasi,
            'jeniskendaraan'=>$this->kendaraan->jeniskendaraan->nama */
        ];
    }
}
