<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'reference'      => $this->reference,
            'barcode'        => $this->barcode,
            'quantity'       => $this->quantity,
            'min_quantity'   => $this->min_quantity,
            'status'         => $this->status,
            'article_status' => $this->article_status,
            'unit'           => $this->unit,
            'brand'          => $this->brand,
            'nature'         => $this->nature,
            'supplier'       => $this->supplier,
            'ocp_code'       => $this->ocp_code,
            'description'    => $this->description,
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'category_id'    => $this->category_id,
            'stock_level'    => $this->stockLevel(),
            'created_at'     => $this->created_at?->toDateString(),
            'deleted_at'     => $this->deleted_at?->toDateTimeString(),
        ];
    }

    private function stockLevel(): string
    {
        if ($this->quantity === 0) {
            return 'critical';
        }

        if ($this->quantity <= $this->min_quantity) {
            return 'low';
        }

        return 'good';
    }
}
