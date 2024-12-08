<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'type' => $this->type, 
            'user' => new UserResource($this->user),
            'subcategory_name' => $this->subcategory ? $this->subcategory->name : null,
            'incident_location' => $this->incident_location,
            'incident_date' => $this->formatDate($this->incident_date),
            'status' => $this->status,
        ];
    }

    public function formatDate($date)
    {
        return $date ? Carbon::parse($date)->format('F j, Y') : null;
    }
}
