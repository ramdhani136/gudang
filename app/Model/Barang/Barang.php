<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Listrso;

class Barang extends Model
{
    protected $table="barang";
    protected $guarded=[];

    public function listrso(){
        return $this->hasMany(listrso::class,'kode_barang','kode');
    }
}
