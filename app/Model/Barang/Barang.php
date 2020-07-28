<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Listrso;
use App\Model\Sales\Custprice;

class Barang extends Model
{
    protected $table="barang";
    protected $guarded=[];

    public function listrso(){
        return $this->hasMany(listrso::class,'kode_barang','kode');
    }

    public function custprice(){
        return $this->hasMany(Custprice::class,'kode_barang','kode');
    }
}
