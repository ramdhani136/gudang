<?php

namespace App\Model\Bck;

use Illuminate\Database\Eloquent\Model;

class Listbck extends Model
{
    protected $table="list_bck";
    protected $guarded=[];

    public function bck(){
        return $this->hasMany(listbck::class,'nomor_bck','bck');
    }
}
