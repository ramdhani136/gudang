<?php

namespace App\Model\Po;

use Illuminate\Database\Eloquent\Model;
use App\Model\Supplier\Supplier;
use App\Model\Purchase\Purchasing;

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
}
