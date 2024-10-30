<?php

namespace App\Http\Resources\Post;

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
            'subcategory_name' => $this->subcategory ? $this->subcategory->name : null,
            'incident_location' => $this->incident_location,
            'incident_date' => $this->formatDate($this->incident_date)
        ];
    }

    public function formatDate($date)
    {
        return $date ? Carbon::parse($date)->format('F j, Y') : null;
    }
}
