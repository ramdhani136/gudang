<?php

namespace App\Model\customer;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;

class Customer extends Model
{
    protected $guarded=[];

    public function rso(){
        return $this->hasMany(Rso::class,'kode_customer','kode');
    }
}

