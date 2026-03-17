<?php

namespace App\Http\Resources\AgentVisits;

use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanPromotionProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'barcode' => $this->barcode,
            'product_variation_id' => $this->product_variation_id,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
