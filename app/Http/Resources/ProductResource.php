<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'condition' => $this->condition,
            'description_title' => $this->description_title,
            'description_text' => $this->description_text,
            'price' => $this->price,
            'published' => $this->published,
        ];
    }
}
