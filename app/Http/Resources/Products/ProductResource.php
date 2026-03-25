<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Categories\CategoriesResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id ?? '',
            'id_erp' => $this->id_erp ?? '',
            'product_code' => $this->product_code ?? '',
            'barcode' => $this->standard ? $this->standard->barcode : '',
            'name' => $this->name ?? '',
            'name_ar' => $this->name_ar ?? '',
            'description_ar' => $this->description_ar ?? '',
            'category' => $this->category ? new CategoriesResource($this->category) : null,
            'image' => $this->standard ? $this->standard->image : '',


        ];
    }
}
