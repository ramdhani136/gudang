<?php

namespace App\Model\Bbk;

use Illuminate\Database\Eloquent\Model;
use App\Model\Bbk\Bbk;
use App\Model\Barang\Barang;

class Listbbk extends Model
{
    protected $table="listbbk";
    protected $guarded=[];

    public function bbk(){
        return $this->belongsTo(Bbk::class,'nomor_bbk','bbk');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }
}
