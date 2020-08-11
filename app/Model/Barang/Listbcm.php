<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Barang\Bcm;
use App\Model\Barang\Barang;

class Listbcm extends Model
{
    protected $table="list_bcm";
    protected $guarded=[];

    public function bcm(){
        return $this->belongsTo(Bcm::class,'nomor_bcm','bcm');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }
}
