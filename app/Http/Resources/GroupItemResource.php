<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Barang\Barang;
use App\Model\Rso\Listrso;

class GroupItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $jumlah= Listrso::where('kode_barang',$this->kode_barang)->where('booking','Y')->where('qty_tdktersedia','>',0)->where('nomor_po',$this->nomor_po)->where('so_open','Y')->where('open_po','Y')->sum('qty_tdktersedia');
        $jumlahmasuk= Listrso::where('kode_barang',$this->kode_barang)->where('booking','Y')->where('qty_tdktersedia','>',0)->where('nomor_po',$this->nomor_po)->where('so_open','Y')->where('open_po','Y')->sum('qty_masuk');
        $groupList= Barang::where('kode',$this->kode_barang)->get();

        return [
            'nomor_po'=>$this->nomor_po,
            'harga_supplier'=>$this->harga_supplier,
            'kode_barang'=>$this->kode_barang,
            'nama'=>$groupList[0]->nama,
            'satuan'=>$groupList[0]->satuan, 
            'subtotal'=>($jumlah*$this->harga_supplier),
            'jumlah'=>$jumlah,
            'masuk'=>$jumlahmasuk,
            'sisapo'=>$jumlah-$jumlahmasuk,
        ];
    }
}
