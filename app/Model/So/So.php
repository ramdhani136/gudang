<?php

namespace App\Model\So;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;

class So extends Model
{
    protected $table="so";
    protected $guarded=[];

    public function rso(){
        return $this->belongsTo(Rso::class,'nomor_rso','nomor_rso');
    }
}
