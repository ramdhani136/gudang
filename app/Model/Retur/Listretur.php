<?php

namespace App\Model\Retur;

use Illuminate\Database\Eloquent\Model;
use App\Model\Retur\Retur;
use App\Model\Barang\Barang;

class Listretur extends Model
{
    protected $table="listretur";
    protected $guarded=[];

    public function retur(){
        return $this->belongsTo(retur::class,'nomor_retur','nomor_retur');
    }

    public function barang(){
        return $this->belongsTo(barang::class,'kode_barang','kode');
    }
}
