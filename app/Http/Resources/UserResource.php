<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return 
        [
            'id'=>$this->id,
            'username'=>$this->username,
            'name'=>$this->name,
            'email'=>$this->email,
            'email_verified_at'=>$this->email_verified_at,
            'sales'=>$this->sales,
            'kordisales'=>$this->kordisales,
            'susales'=>$this->susales,
            'inventory'=>$this->inventory,
            'incoming'=>$this->incoming,
            'incomingspv'=>$this->incomingspv,
            'distribusi'=>$this->distribusi,
            'purch'=>$this->purch,
            'suppurch'=>$this->suppurch,
            'acc'=>$this->acc,
            'warehouse'=>$this->warehouse,
            'superadmin'=>$this->superadmin,   
            'kode_groupso'=>$this->kode_groupso,
        ];
    }
}
