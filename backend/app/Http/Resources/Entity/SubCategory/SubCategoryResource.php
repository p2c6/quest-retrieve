<?php

namespace App\Http\Resources\Entity\SubCategory;

use App\Http\Resources\Entity\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'category' => new CategoryResource($this->category), 
            'name' => $this->name,
        ];
    }
}
