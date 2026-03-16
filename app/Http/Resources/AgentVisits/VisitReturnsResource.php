<?php

namespace App\Http\Resources\AgentVisits;

use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitReturnsResource extends JsonResource
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
            // 'products' => $this->whenLoaded('returnItems', function () {
            //     $products = $this->returnItems->pluck('product')->filter();

            //     return $products->isNotEmpty()
            //         ? ProductResource::collection($products)
            //         : null;
            // }),

            'product' => new ProductResource($this->product ?? null),

            'expirations' => ReturnsExpirationDatesResource::collection($this->expirationDates)
        ];
    }
}
