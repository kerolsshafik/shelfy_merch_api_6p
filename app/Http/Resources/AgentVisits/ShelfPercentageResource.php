<?php

namespace App\Http\Resources\AgentVisits;
use App\Http\Resources\Categories\CategoriesResource;
use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelfPercentageResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return new CategoriesResource($this->category);
            }),
            'percentage' => $this->percentage,
        ];
    }
}