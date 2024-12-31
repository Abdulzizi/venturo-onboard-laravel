<?php

namespace App\Http\Resources\Customer;

use App\Models\CustomerModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'photo_url' => $this->photo ? url('storage/' . $this->photo) : null,
            'phone' => $this->phone,
            'email' => $this->user->email,
            'm_user_id' => $this->m_user_id
        ];
    }
}
