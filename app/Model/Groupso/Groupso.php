<?php

namespace App\Model\Groupso;

use App\Model\Rso\Rso;
use Illuminate\Database\Eloquent\Model;

class Groupso extends Model
{
    protected $table = "groupso";
    protected $guarded=[];

    public function rso(){
        return $this->hasMany(Rso::class,'kode_groupso','kode');
    }
}
