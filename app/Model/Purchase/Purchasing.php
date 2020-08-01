<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Model\Po\Po;

class Purchasing extends Model
{
    protected $table="purchasing";
    protected $guarded=[];

    public function po(){
        return $this->belongsTo(Po::class,'nip_purchasing','nip');
    }
}
