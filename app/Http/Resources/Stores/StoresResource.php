<?php

namespace App\Http\Resources\Stores;

use Illuminate\Http\Resources\Json\JsonResource;

class StoresResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name'=>$this->name,
            'phone'=>$this->phone,
            'address'=>$this->address,
            'city'=>$this->city,
            'region'=>$this->region,
            'country'=>$this->country,
            'email'=>$this->email,
            'store_name'=>$this->store_name,
            'segment'=>$this->segment,
            'lat'=>$this->lat,
            'lng'=>$this->lng
        ];
    }
}
