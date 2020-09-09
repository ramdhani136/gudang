<?php

namespace App\Model\So;

use Illuminate\Database\Eloquent\Model;
use App\Model\So\So;
use App\Model\Barang\Barang;

class Listso extends Model
{
    protected $table="listso";
    protected $guarded =[];

    public function so(){
        return $this->belongsTo(So::class,'nomor_so','nomor_so');
    }

    public function barang(){
        return $this->belongsTo(barang::class,'kode_barang','kode');
    }
}
