<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'm_customer_id' => $this->m_customer_id,
            'customer_name' => isset($this->customer) ? $this->customer->name : "",
            'date' => $this->date,
            'details' => SaleDetailResource::collection($this->details),
        ];
    }
}
