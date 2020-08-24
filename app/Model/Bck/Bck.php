<?php

namespace App\Model\Bck;

use Illuminate\Database\Eloquent\Model;
use App\Model\So\So;
use App\Model\Bck\Listbck;
use App\Model\Bbk\Bbk;
use App\Model\Kendaraan\Kendaraan;

class Bck extends Model
{
    protected $table="bck";
    protected $guarded=[];

    public function so(){
        return $this->belongsTo(So::class,'nomor_so','nomor_so');
    }

    public function listbck(){
        return $this->hasMany(listbck::class,'nomor_bck','bck');
    }

    public function kendaraan(){
        return $this->belongsTo(kendaraan::class,'id_kendaraan','id');
    }

    public function bbk(){
        return $this->hasMany(Bbk::class,'nomor_bck','bck');
    }
}
