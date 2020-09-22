<?php

namespace App\Model\Barang;

use App\Model\Barang\Barang;
use Illuminate\Database\Eloquent\Model;

class Kategoribarang extends Model
{
    protected $table ="kategoribarang";
    protected $guarded=[];

    public function barang(){
        return $this->hasMany(Barang::class,'id_kategori','id');
    }
}
