<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class listprResource extends JsonResource
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
            'nomor_pr'=>$this->nomor_pr,
            'kode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'qty'=>$this->qty,
            'po'=>$this->po,
            'bbm'=>$this->bbm,
            'sisa'=>$this->sisa,
            'close'=>$this->close,
            'keterangan'=>$this->keterangan,
            'tgl_estimasi'=>$this->tgl_estimasi,
            'status'=>$this->status,
            'alastolak'=>$this->alastolak,
            'accdate'=>$this->accdate,
        ];
    }
}
