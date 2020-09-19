<?php

namespace App\Model\History;

use App\User;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table="history";
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'id_user','id');
    }
}

