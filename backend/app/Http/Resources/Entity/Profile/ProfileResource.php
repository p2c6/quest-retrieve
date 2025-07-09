<?php

namespace App\Http\Resources\Entity\Profile;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'birthday' => $this->birthday 
            ? Carbon::parse($this->birthday)->format('M d, Y') 
            : null,
            'contact_no' => $this->contact_no,
        ];
    }
}
