<?php

namespace App\Model\Bbm;

use Illuminate\Database\Eloquent\Model;
use App\Model\Bbm\Bbm;
use App\Model\Barang\Barang;


class Listbbm extends Model
{
    protected $table="listbbm";
    protected $guarded=[];

    public function barang(){
        return $this->belongsTo(barang::class,'kode_barang','kode');
    }
}
