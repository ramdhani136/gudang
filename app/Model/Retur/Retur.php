<?php

namespace App\Model\Retur;

use Illuminate\Database\Eloquent\Model;
use App\Model\So\So;
use App\Model\Retur\Listretur;

class Retur extends Model
{
    protected $table="retur";
    protected $guarded=[];

    public function so(){
        return $this->belongsTo(So::class,'nomor_so','nomor_so');
    }

    public function listretur(){
        return $this->hasMany(listretur::class,'nomor_retur','nomor_retur');
    }
}
