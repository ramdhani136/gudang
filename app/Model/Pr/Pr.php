<?php

namespace App\Model\Pr;
use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Pr\Listpr;

class Pr extends Model
{
    protected $table="pr";
    protected $guarded=[];

    public function listpr(){
        return $this->hasMany(Listpr::class,'nomor_pr','nomor_pr');
    }

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }

}
