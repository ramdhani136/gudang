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
            'no_reg'=>$this->no_reg,
            'nip_sales'=>$this->nip_sales,
            'sales'=>$this->sales->nama,
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
