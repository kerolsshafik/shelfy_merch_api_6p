<?php

namespace App\Http\Resources\PosMaterial;

use Illuminate\Http\Resources\Json\JsonResource;

class PosmResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'store_id' => $this->store_id,
            'store_type' => $this->store_type,
            'images' => PosmImagesResource::collection($this->whenLoaded('images')),
        ];
    }
}
