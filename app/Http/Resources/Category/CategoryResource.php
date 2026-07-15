<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'articles_count' => $this->articles_count ?? $this->articles()->count(),
            'total_stock'    => $this->total_stock ?? (int) $this->articles()->sum('quantity'),
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
