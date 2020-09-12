<?php

namespace App\Model\customer;

use App\Model\Pr\Pr;
use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Sales\Sales;
use App\Model\Sales\Custprice;

class Customer extends Model
{
    protected $guarded=[];

    public function rso(){
        return $this->hasMany(Rso::class,'kode_customer','kode');
    }

    public function Sales(){
        return $this->belongsTo(Sales::class,'nip_sales','nip');
    }

    public function custprice(){
        return $this->hasMany(Custprice::class,'kode_customer','kode');
    }

    public function pr(){
        return $this->hasMany(Pr::class,'kode_customer','kode');
    }
}

