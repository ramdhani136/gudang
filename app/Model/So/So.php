<?php

namespace App\Model\So;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Rso\Listrso;
use App\Model\Bck\Bck;
use App\Model\Retur\Retur;

class So extends Model
{
    protected $table="so";
    protected $guarded=[];

    public function rso(){
        return $this->belongsTo(Rso::class,'nomor_rso','nomor_rso');
    }

    public function bck(){
        return $this->hasMany(So::class,'nomor_so','nomor_so');
    }

    public function retur(){
        return $this->hasMany(retur::class,'nomor_so','nomor_so');
    }


}
