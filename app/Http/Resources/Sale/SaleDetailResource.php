<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailResource extends JsonResource
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
            'm_product_id' => $this->m_product_id,
            'product_name' => isset($this->product) ? $this->product->name : "",
            'm_product_detail_id' => $this->m_product_detail_id,
            'total_item' => $this->total_item,
            'price' => $this->price,
        ];
    }
}
