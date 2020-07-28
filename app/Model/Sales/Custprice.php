<?php

namespace App\Model\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Model\Sales\Sales;
use App\Model\Barang\Barang;
use App\Model\customer\Customer;

class Custprice extends Model
{
    protected $table="custprice";
    protected $guarded=[];

    public function Sales(){
        return $this->belongsTo(Sales::class,'nip_sales','nip');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'kode_customer','kode');
    }
}
