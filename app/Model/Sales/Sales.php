<?php

namespace App\Model\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Model\Customer\Customer;

class Sales extends Model
{
    protected $table="sales";
    protected $guarded=[];

    public function customer(){
        return $this->hasMany(Customer::class,'nip_sales','nip');
    }
}
