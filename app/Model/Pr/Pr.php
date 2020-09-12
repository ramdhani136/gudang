<?php

namespace App\Model\Pr;

use App\Model\customer\Customer;
use Illuminate\Database\Eloquent\Model;

class Pr extends Model
{
    protected $table="pr";
    protected $guarded=[];

    public function pr(){
        return $this->hasMany(Pr::class,'nomor_pr','nomor_pr');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'kode_customer','kode');
    }
}
