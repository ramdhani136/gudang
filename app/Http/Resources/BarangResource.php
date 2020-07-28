<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Rso\Listrso;

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

        $data=Listrso::all();
        $book= Listrso::where('kode_barang',$this->kode)->where('booking','Y')->sum('qty_tersedia');
        

        return 
        [
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'qty'=>$this->qty,
            'satuan'=>$this->satuan,
            'dibooking'=>$book,
            'harga'=>$this->harga, 
            'min_stok'=>$this->min_stok, 
            'max_stok'=>$this->max_stok, 
            'kubikasi'=>$this->kubikasi, 
            'kg'=>$this->kg, 
        ];
    }
}
