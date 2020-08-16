<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Rso\Listrso;
use App\Model\Barang\Bcm;


class ListBcmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $po= Bcm::where('bcm',$this->nomor_bcm)->first();

        $jumlah= Listrso::where('kode_barang',$this->kode_barang)->where('booking','Y')->where('qty_tdktersedia','>',0)->where('nomor_po',$po->nomor_po)->where('so_open','Y')->sum('qty_tdktersedia');
        $jumlahmasuk= Listrso::where('kode_barang',$this->kode_barang)->where('booking','Y')->where('qty_tdktersedia','>',0)->where('nomor_po',$po->nomor_po)->where('so_open','Y')->sum('qty_masuk');

        return [
            'id'=>$this->id,
            'nomor_po'=>$po->nomor_po,
            'nomor_bcm'=>$this->nomor_bcm,
            'kode_barang'=>$this->kode_barang,
            'qty'=>$this->qty,
            'keterangan'=>$this->keterangan,
            'nama_barang'=>$this->barang->nama,
            'satuan'=>$this->barang->satuan,
            'qty_po'=>$jumlah,
            'masuk'=>$jumlahmasuk,
            'sisapo'=>$jumlah-$jumlahmasuk, 
            'sisaporeal'=>$this->sisapo, 
        ];
    }
}
