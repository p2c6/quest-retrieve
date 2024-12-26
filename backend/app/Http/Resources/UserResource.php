<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->id,
            'email' => $this->email,
            'last_name' => $this->profile?->last_name,
            'first_name' => $this->profile?->first_name,
            'birthday' => $this->profile?->birthday,
            'contact_no' => $this->profile?->contact_no,
            'role_id' => $this->role_id,
        ];
    }
}
