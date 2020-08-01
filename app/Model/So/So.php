<?php

namespace App\Model\So;

use Illuminate\Database\Eloquent\Model;
use App\Model\Rso\Rso;
use App\Model\Rso\Listrso;

class So extends Model
{
    protected $table="so";
    protected $guarded=[];

    public function rso(){
        return $this->belongsTo(Rso::class,'nomor_rso','nomor_rso');
    }


}
