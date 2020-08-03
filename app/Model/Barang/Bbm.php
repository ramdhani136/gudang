<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Po\Po;

class Bbm extends Model
{
    protected $table="bbm";
    protected $guarded=[];

    public function po(){
        return $this->belongsTo(Po::class,'nomor_po','nomor_po');
    }
}

