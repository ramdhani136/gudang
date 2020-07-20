<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListRsoResource extends JsonResource
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
            'lno_rso'=>$this->nomor_rso,
            'lkode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'nama_customer'=>$this->rso->customer->nama,
            'tanggal_rso'=>$this->rso->tanggal_rso,
            'qty'=>$this->qty,
            'satuan'=>$this->barang->satuan,
            'status'=>$this->status,
            'tgl_datang'=>$this->tanggal_datang,
            'acc'=>$this->acc,
            'catatan'=>$this->catatan,
            'qty_tersedia'=>$this->qty_tersedia,
            'qty_tdktersedia'=>$this->qty_tdktersedia,
            'pucrh'=>$this->purch,

        ];
    }
}
