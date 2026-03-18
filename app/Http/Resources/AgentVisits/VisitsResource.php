<?php

namespace App\Http\Resources\AgentVisits;

use App\Http\Resources\AgentVisits\ScanPackProductResource;
use App\Http\Resources\AgentVisits\ScanPromotionProductResource;
use App\Http\Resources\PosMaterial\PosMaterialResource;
use App\Http\Resources\PosMaterial\PosmResource;
use App\Http\Resources\Stores\StoresResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitsResource extends JsonResource
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
            'store' => StoresResource::make($this->store),
            'is_must' => $this->is_must,
            'order' => $this->order,
            'is_disabled' => $this->end_time != null ? true : false,
            'is_attended' => $this->agentAttendances ? true : false,
            // 'return_items' => VisitReturnsResource::collection($this->returnItems),
            // 'attendance' => new AttendanceResource($this->agentAttendances),
            // 'pos_materials' => PosMaterialResource::collection($this->posMaterials),
            // 'visit_osa' => VisitOsaResource::collection($this->osaVisits),
            // 'visit_grouped_items' => VisitItemResource::collection($this->visitItems)
            'return_items' => VisitReturnsResource::collection($this->whenLoaded('returnItems')),
            'attendance' => new AttendanceResource($this->whenLoaded('agentAttendances')),
            'pos_materials' => PosMaterialResource::collection($this->whenLoaded('posMaterials')),
            'visit_osa' => VisitOsaResource::collection($this->whenLoaded('osaVisits')),
            'visit_grouped_items' => VisitItemResource::collection($this->whenLoaded('visitItems')),
            'scanPackProducts' => ScanPackProductResource::collection($this->whenLoaded('scanPackProducts')),
            'scanPromotionProducts' => ScanPromotionProductResource::collection($this->whenLoaded('scanPromotionProducts')),
            'posMs' => PosmResource::collection($this->whenLoaded('posMs')),
            'productPrices' => ProductPriceResource::collection($this->whenLoaded('productPrices')),
        ];

    }
}
