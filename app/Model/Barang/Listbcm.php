<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Barang\Bcm;

class Listbcm extends Model
{
    protected $table="list_bcm";
    protected $guarded=[];

    public function bcm(){
        return $this->belongsTo(Bbm::class,'nomor_bcm','bcm');
    }
}
