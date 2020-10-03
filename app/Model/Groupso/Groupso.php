<?php

namespace App\Model\Groupso;

use App\Model\Rso\Rso;
use App\Model\So\So;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Groupso extends Model
{
    protected $table = "groupso";
    protected $guarded=[];

    public function rso(){
        return $this->hasMany(Rso::class,'kode_groupso','kode');
    }

    public function user(){
        return $this->hasMany(User::class,'kode_groupso','kode');
    }

    public function so(){
        return $this->hasMany(So::class,'kode_groupso','kode');
    }
}
