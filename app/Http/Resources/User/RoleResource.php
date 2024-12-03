<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "access" => isset($this->access) ? json_decode($this->access, true) : [],
            "created_at" => $this->created_at ? $this->created_at->toDateTimeString() : null,
            "updated_at" => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
