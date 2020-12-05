<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Message;
use App\Model\Groupso\Groupso;
use App\Model\History\History;
use App\Model\Po\Po;
use App\Model\Pr\Pr;
use App\Model\Rso\Rso;
use App\Model\Sales\Custprice;
use App\Model\So\So;
use App\Sales\Formprice;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','username','sales','susales','inventory','incoming','distribusi','purch','suppurch',
        'acc','superadmin','warehouse','kode_groupso',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function history(){
        return $this->hasMany(History::class,'id_user','id');
    }

    public function rso(){
        return $this->hasMany(Rso::class,'id_user','id');
    }

    public function so(){
        return $this->hasMany(So::class,'id_user','id');
    }

    public function custprice(){
        return $this->hasMany(Custprice::class,'id_user','id');
    }
    public function formprice(){
        return $this->hasMany(Formprice::class,'id_user','id');
    }

    public function groupso(){
        return $this->belongsTo(Groupso::class,'kode_groupso','kode');
    }

    public function po(){
        return $this->hasMany(Po::class,'id_user','id');
    }

    public function pr(){
        return $this->hasMany(Pr::class,'id_user','id');
    }


}
