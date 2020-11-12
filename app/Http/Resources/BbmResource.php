<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Barang\Barang;

class BbmResource extends JsonResource
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
            'bbm'=>$this->bbm,
            'nomor_bcm'=>$this->nomor_bcm,
            'tanggal'=>$this->tanggal,
            'keterangan'=>$this->keterangan,
            'status'=>$this->status,
            'nomor_po'=>$this->bcm->nomor_po,
            'nopol'=>$this->bcm->nopol,
            'kode_supplier'=>$this->bcm->po->supplier->kode_supplier,
            'supplier'=>$this->bcm->po->supplier->nama,
            'id_user'=>$this->bcm->po->id_user,
            'user'=>$this->bcm->po->user->name,
            'request'=>$this->request,
            'alastolak'=>$this->alastolak,
            'keteranganedit'=>$this->keteranganedit,
        ];
    }
}
