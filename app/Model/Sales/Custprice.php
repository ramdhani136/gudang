<?php

namespace App\Model\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Model\Barang\Barang;
use App\Model\customer\Customer;
use App\User;

class Custprice extends Model
{
    protected $table="custprice";
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'kode_customer','kode');
    }
}
