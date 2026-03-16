<?php

namespace App\Http\Resources\PosMaterial;

use Illuminate\Http\Resources\Json\JsonResource;

class PosmImagesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->image_path,
        ];
    }
}
