<?php

namespace App\Model\So;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Rso\Listrso;
use App\Model\Bck\Bck;
use App\Model\Groupso\Groupso;
use App\Model\Retur\Retur;
use App\Model\So\Listso;
use App\User;

class So extends Model
{
    protected $table="so";
    protected $guarded=[];

    public function rso(){
        return $this->belongsTo(Rso::class,'nomor_rso','nomor_rso');
    }

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }

    public function groupso(){
        return $this->belongsTo(Groupso::class,'kode_groupso','kode');
    }

    public function bck(){
        return $this->hasMany(So::class,'nomor_so','nomor_so');
    }

    public function retur(){
        return $this->hasMany(retur::class,'nomor_so','nomor_so');
    }

    public function listso(){
        return $this->hasMany(listso::class,'nomor_so','nomor_so');
    }


}
