<?php

namespace App\Model\Bck;

use Illuminate\Database\Eloquent\Model;
use App\Model\Bck\Bck;
use App\Model\Barang\Barang;

class Listbck extends Model
{
    protected $table="list_bck";
    protected $guarded=[];

    public function bck(){
        return $this->belongsTo(Bck::class,'nomor_bck','bck');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }
}
