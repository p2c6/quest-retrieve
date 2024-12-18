<?php

namespace App\Http\Resources\Entity\SubCategory;

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
            'category' => $this->category ? [
                                                'id' => $this->category->id, 
                                                'category_name' => $this->category->name 
                                            ] : null, 
            'name' => $this->name,
        ];
    }
}
