<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'product_category_id' => $this->m_product_category_id,
            'product_category_name' => isset($this->category) ? $this->category->name : "",
            'is_available' => (string) $this->is_available,
            'description' => $this->description,
            'photo_url' => !empty($this->photo) ? Storage::disk('public')->url($this->photo) : Storage::disk('public')->url('../assets/img/no-image.png'),
            'details' => ProductDetailResource::collection($this->details),
        ];
    }
}
