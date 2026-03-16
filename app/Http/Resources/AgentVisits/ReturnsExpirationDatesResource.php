<?php

namespace App\Http\Resources\AgentVisits;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturnsExpirationDatesResource extends JsonResource
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
            'expiration_date'=>$this->expiration_date,
            'quantity'=>$this->quantity
        ];
    }
}
