<?php

namespace App\Model\Po;

use Illuminate\Database\Eloquent\Model;
use App\Model\Supplier\Supplier;
use App\Model\Purchase\Purchasing;
use App\Model\Barang\Bbm;

class Po extends Model
{
    protected $table="po";
    protected $guarded=[];

    public function supplier(){
        return $this->belongsTo(Supplier::class,'kode_supplier','kode_supplier');
    }

    public function purchasing(){
        return $this->belongsTo(Purchasing::class,'nip_purchasing','nip');
    }

    public function bbm(){
        return $this->hasMany(Bbm::class,'nomor_po','nomor_po');
    }
}
