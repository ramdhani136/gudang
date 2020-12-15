<?php

namespace App\Http\Resources;

use App\Model\Barang\Barang;
use App\Model\Pr\Listpr;
use Illuminate\Http\Resources\Json\JsonResource;

class ListprGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $jumlah= Listpr::where('kode_barang',$this->kode_barang)->where('status','Y')->where('close','N')->sum('sisa');
        $allopen= Listpr::where('kode_barang',$this->kode_barang)->where('status','Y')->sum('po');
        $groupList= Barang::where('kode',$this->kode_barang)->get();
        
        return 
        [
            'kode_barang'=>$this->kode_barang,
            'nama'=>$groupList[0]->nama,
            'satuan'=>$groupList[0]->satuan, 
            'jumlah'=>$jumlah,
            'open'=>$allopen,  
 
        ];
    }
}
