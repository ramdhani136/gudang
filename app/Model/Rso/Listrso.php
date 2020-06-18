<?php

namespace App\Model\Rso;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Barang\Barang;

class Listrso extends Model
{
    protected $table="listrso";
    protected $guarded=[];

    public function rso(){
        return $this->belongsTo(Rso::class,'id_rso','id');
    }

    public function barang(){
        return $this->belongsTo(barang::class,'id_barang','id');
    }
}
