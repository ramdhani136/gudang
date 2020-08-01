<?php

namespace App\Model\Supplier;

use Illuminate\Database\Eloquent\Model;
use App\Model\Po\Po;

class Supplier extends Model
{
    protected $guarded=[];
    
    public function po(){
        return $this->hasMany(Po::class,'kode_supplier','kode_supplier');
    }
}
