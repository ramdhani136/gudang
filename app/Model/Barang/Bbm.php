<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Po\Po;
use App\Model\Barang\listbbm;

class Bbm extends Model
{
    protected $table="bbm";
    protected $guarded=[];

    public function po(){
        return $this->belongsTo(Po::class,'nomor_po','nomor_po');
    }

    public function listbbm(){
        return $this->hasMany(listbbm::class,'nomor_bbm','bbm');
    }
}

