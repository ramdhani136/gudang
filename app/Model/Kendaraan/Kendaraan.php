<?php

namespace App\Model\Kendaraan;

use Illuminate\Database\Eloquent\Model;
use App\Model\Bck\Bck;
use App\Model\Kendaraan\JenisKendaraan;

class Kendaraan extends Model
{
    protected $table="kendaraan";
    protected $guarded=[];

    public function bck(){
        return $this->hasMany(Bck::class,'id_kendaraan','id');
    }

    public function jeniskendaraan(){
        return $this->belongsTo(JenisKendaraan::class,'id_jenis','id');
    }
}
