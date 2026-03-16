<?php

namespace App\Http\Resources\AgentVisits;

use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitItemResource extends JsonResource
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
            'id'=>$this->id,
            'visit_id'=>$this->visit_id,
            'product_ids'=>$this->product_ids,
            'category_id'=>$this->category_id,
            // 'products'=>
            'products'=> ProductResource::collection($this->VisitItemProducts->pluck('product'))
        ];
    }
}
