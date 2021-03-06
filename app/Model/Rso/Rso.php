<?php

namespace App\Model\Rso;

use Illuminate\Database\Eloquent\Model;
use App\Model\Sales\Sales;
use App\Model\Customer\Customer;
use App\Model\Groupso\Groupso;
use App\Model\Rso\Listrso;
use App\Model\So\So;
use App\User;

class Rso extends Model
{
    protected $table="rso";
    protected $guarded=[];

    public function sales(){
        return $this->belongsTo(Sales::class,'nip_sales','nip');
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'kode_customer','kode');
    }

    public function listrso(){
        return $this->hasMany(listrso::class,'nomor_rso','nomor_rso');
    }

    public function so(){
        return $this->hasMany(So::class,'nomor_rso','nomor_rso');
    }


    public function groupso(){
        return $this->belongsTo(Groupso::class,'kode_groupso','kode');
    }

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }
}
