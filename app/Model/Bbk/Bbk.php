<?php

namespace App\Model\Bbk;

use Illuminate\Database\Eloquent\Model;
use App\Model\Bck\Bck;
use App\Model\Bbk\Listbbk;

class Bbk extends Model
{
    protected $table="bbk";
    protected $guarded=[];

    public function bck(){
        return $this->belongsTo(Bck::class,'nomor_bck','bck');
    }

    public function listbbk(){
        return $this->hasMany(listbbk::class,'nomor_bbk','bbk');
    }
}
