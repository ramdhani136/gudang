<?php

namespace App\Model\Pr;

use App\Model\Barang\Barang;
use Illuminate\Database\Eloquent\Model;

class Listpr extends Model
{
    protected $table="listpr";
    protected $guarded=[];

    public function pr(){
        return $this->belongsTo(Pr::class,'nomor_pr','nomor_pr');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }

}
