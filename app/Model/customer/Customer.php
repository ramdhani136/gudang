<?php

namespace App\Model\customer;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Sales\Sales;

class Customer extends Model
{
    protected $guarded=[];

    public function rso(){
        return $this->hasMany(Rso::class,'kode_customer','kode');
    }

    public function Sales(){
        return $this->belongsTo(Sales::class,'nip_sales','nip');
    }
}

