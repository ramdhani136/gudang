<?php

namespace App\Model\Rso;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Barang\Barang;
use App\Model\So\So;

class Listrso extends Model
{
    protected $table="listrso";
    protected $guarded=[];

    public function rso(){
        return $this->belongsTo(Rso::class,'nomor_rso','nomor_rso');
    }

    public function barang(){
        return $this->belongsTo(barang::class,'kode_barang','kode');
    }

}
