<?php

namespace App\Model\Kendaraan;

use Illuminate\Database\Eloquent\Model;
use App\Model\Kendaraan\Kendaraan;

class JenisKendaraan extends Model
{
    protected $table="jeniskendaraan";
    protected $guarded=[];

    public function kendaraan(){
        return $this->hasMany(kendaraan::class,'id_jenis','id');
    }
}
