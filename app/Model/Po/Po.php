<?php

namespace App\Model\Po;

use App\Model\Po\Listpo;
use App\Model\Barang\Bbm;
use App\Model\Supplier\Supplier;
use App\Model\Purchase\Purchasing;
use Illuminate\Database\Eloquent\Model;

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

    public function listpo(){
        return $this->hasMany(Listpo::class,'nomor_po','nomor_po');
    }
}
