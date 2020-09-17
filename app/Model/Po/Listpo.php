<?php

namespace App\Model\Po;

use App\Model\Barang\Barang;
use App\Model\Po\Po;
use Illuminate\Database\Eloquent\Model;

class Listpo extends Model
{
    protected $table="listpo";
    protected $guarded=[];

    public function po(){
        return $this->belongsTo(Po::class,'nomor_po','nomor_po');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'kode_barang','kode');
    }
}
