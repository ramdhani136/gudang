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
        $oncom= Listrso::where('kode_barang',$this->kode)->where('booking','Y')->sum('qty_tersedia');
        

        return 
        [
            'kode'=>$this->kode,
            'nama'=>$this->nama,
            'qty'=>$this->qty,
            'satuan'=>$this->satuan,
            'dibooking'=>($oncom) 
        ];
    }
}
