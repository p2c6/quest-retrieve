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
            // 'user' => new UserResource($this->user),
            'subcategory_id' => $this->subcategory_id,
            'subcategory' => $this->subcategory ?   [
                                                        'id' => $this->subcategory->id ,
                                                        'name' => $this->subcategory->name
                                                    ] : null,
            'incident_location' => $this->incident_location,
            'incident_date' =>  [
                                    'original' => $this->originalDate($this->incident_date), 
                                    'for_human' =>$this->formatDate($this->incident_date),
                                ],
            'status' => $this->status,
        ];
    }

    public function formatDate($date)
    {
        return $date ? Carbon::parse($date)->format('F j, Y') : null;
    }

    public function originalDate($date)
    {
        $date = Carbon::createFromFormat('F j, Y', $date);
        $formattedDate = $date->format('Y-m-d');

        return $formattedDate;
    }
}
