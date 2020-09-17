<?php

namespace App\Http\Resources;

use App\Model\Barang\Barang;
use App\Model\So\Listso;
use Illuminate\Http\Resources\Json\JsonResource;

class SoGroupResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $jumlah= Listso::where('kode_barang',$this->kode_barang)->where('statusso','Tidak Tersedia')->where('statuspo','N')->where('openso','Y')->sum('sisapo');
        $groupList= Barang::where('kode',$this->kode_barang)->get();

       return 
        [
            'kode_barang'=>$this->kode_barang,
            'nama'=>$groupList[0]->nama,
            'satuan'=>$groupList[0]->satuan, 
            'jumlah'=>$jumlah,
        ];
    }
}
