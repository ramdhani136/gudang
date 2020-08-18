<?php

namespace App\Model\Bbm;

use Illuminate\Database\Eloquent\Model;
use App\Model\Barang\Bcm;
use App\Model\Bbm\Listbbm;

class Bbm extends Model
{
    protected $table="bbm";
    protected $guarded=[];

    public function barang(){
        return $this->hasMany(Barang::class,'kode_barang','kode');
    }

    public function bcm(){
        return $this->belongsTo(Bcm::class,'nomor_bcm','bcm');
    }

    public function listbbm(){
        return $this->hasMany(Listbbm::class,'nomor_bbm','bbm');
    }
}
