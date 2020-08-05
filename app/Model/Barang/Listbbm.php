<?php

namespace App\Model\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Model\Barang\Bbm;

class Listbbm extends Model
{
    protected $table="list_bbm";
    protected $guarded=[];

    public function bbm(){
        return $this->belongsTo(Bbm::class,'nomor_bbm','bbm');
    }
}
