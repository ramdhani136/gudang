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
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'qty'=>$this->qty,
            'satuan'=>$this->satuan,
            'id_kategori'=>$this->kategoribarang->id,
            'kategori'=>$this->kategoribarang->nama,
           /*  'dibooking'=>$book, */
            'harga'=>$this->harga, 
            'min_stok'=>$this->min_stok, 
            'max_stok'=>$this->max_stok, 
            'kubikasi'=>$this->kubikasi, 
            'kg'=>$this->kg, 
            'aktif'=>$this->aktif,
        ];
    }
}
