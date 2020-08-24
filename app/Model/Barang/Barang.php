<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Listrso;
use App\Model\Sales\Custprice;
use App\Model\Barang\Listbcm;
use App\Model\Bbm\Listbbm;
use App\Model\Bck\Listbck;
use App\Model\Bck\Listbbk;

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

    public function listbcm(){
        return $this->hasMany(listbcm::class,'kode_barang','kode');
    }

    public function listbbm(){
        return $this->hasMany(Listbbm::class,'kode_barang','kode');
    }

    public function listbck(){
        return $this->hasMany(Listbck::class,'kode_barang','kode');
    }

    public function listbbk(){
        return $this->hasMany(listbbk::class,'kode_barang','kode');
    }
}
