<?php

namespace App\Http\Resources\AgentVisits;

use App\Http\Resources\Stores\StoresResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'visit_id' => $this->visit_id,
            'image' => $this->image,
            'lat' => $this->lat,
            'long' => $this->long,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
