<?php

namespace App\Model\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Model\Customer\Customer;
use App\Model\Rso\Rso;
use App\Model\Sales\Custprice;

class Sales extends Model
{
    protected $table="sales";
    protected $guarded=[];

    public function customer(){
        return $this->hasMany(Customer::class,'nip_sales','nip');
    }

        public function rso(){
            return $this->hasMany(Rso::class,'nip_sales','nip');
    }
    
    public function custprice(){
        return $this->hasMany(Custprice::class,'nip_sales','nip');
    }
}
