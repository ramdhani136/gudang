<?php

namespace App\Sales;

use App\Model\customer\Customer;
use App\Model\Groupso\Groupso;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Formprice extends Model
{
    protected $table ="formprice";
    protected $guarded =[];

    public function customer(){
        return $this->belongsTo(Customer::class,'kode_customer','kode');
    }

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }
    
    public function groupso(){
        return $this->belongsTo(Groupso::class,'kode_groupso','kode');
    }
}
