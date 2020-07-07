<?php

namespace App\Model\Rso;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\Customer\Customer;
use App\Model\Rso\Listrso;

class Rso extends Model
{
    protected $table="rso";
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'kode_customer','kode');
    }

    public function listrso(){
        return $this->hasMany(listrso::class,'id_rso','id');
    }

}
