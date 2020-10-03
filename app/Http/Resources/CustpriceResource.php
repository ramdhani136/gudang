<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustpriceResource extends JsonResource
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
            'nomor_price'=>$this->nomor_price,
            'id_user'=>$this->id_user,
            'sales'=>$this->user->name,
            'kode_customer'=>$this->kode_customer,
            'customer'=>$this->customer->nama,
            'kode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'harga'=>$this->harga,
            'status'=>$this->status,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'keterangan'=>$this->keterangan,
            'tanggal'=>$this->tanggal,
            'alastolak'=>$this->alastolak,
        ];
    }
}
