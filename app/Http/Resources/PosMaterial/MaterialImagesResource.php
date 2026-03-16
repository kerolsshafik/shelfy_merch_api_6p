<?php

namespace App\Http\Resources\PosMaterial;

use App\Http\Resources\Stores\StoresResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialImagesResource extends JsonResource
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
            'image' => $this->image_path,
        ];
    }
}
