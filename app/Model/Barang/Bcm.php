<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Po\Po;
use App\Model\Barang\listbcm;
use App\Model\Bbm\Bbm;

class Bcm extends Model
{
    protected $table="bcm";
    protected $guarded=[];

    public function po(){
        return $this->belongsTo(Po::class,'nomor_po','nomor_po');
    }

    public function listbcm(){
        return $this->hasMany(listbbm::class,'nomor_bcm','bcm');
    }

    public function bbm(){
        return $this->hasMany(Bbm::class,'nomor_bcm','bcm');
    }

}

