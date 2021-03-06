<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Model\Rso\Listrso;
use App\Model\So\So;

class listGroupResource extends JsonResource
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
            $oncom= Listrso::where('kode_barang',$this->kode_barang)->sum('qty');
            $so=So::where('nomor_rso',$this->nomor_rso)->where('status',"Acc")->where("statusso","tidaktersedia")->get()->first();
        return 
        [
            'id'=>$this->id,
            'lno_rso'=>$this->nomor_rso,
            'nomor_so'=>$so->nomor_so,
            'lkode_barang'=>$this->kode_barang,
            'nama_barang'=>$this->barang->nama,
            'nama_customer'=>$this->rso->customer->nama,
            'tanggal_rso'=>$this->rso->tanggal_rso,
            'qty'=>$this->qty,
            'satuan'=>$this->barang->satuan,
            'status'=>$this->status,
            'tgl_datang'=>$this->tanggal_datang,
            'catatan'=>$this->catatan,
            'qty_tersedia'=>$this->qty_tersedia,
            'qty_tdktersedia'=>$this->qty_tdktersedia,
            'acc_purch'=>$this->acc_purch,
            'alastolak'=>$this->alastolak,
            'jumlah'=>$oncom,
            'nip_sales'=>$this->rso->nip_sales,
            'kode_customer'=>$this->rso->kode_customer,
            'so_ket'=>$this->rso->keterangan,
            'booking'=>$this->booking,
            'harga'=>$this->harga,
            'id_custprice'=>$this->id_custprice,
            'open_po'=>$this->open_po,
            'so_tersedia'=>$this->so_tersedia,
            'so_tdktersedia'=>$this->so_tdktersedia,
            'harga_supplier'=>$this->harga_supplier,
        ];
    }
}
