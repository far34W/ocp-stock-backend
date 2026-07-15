<?php

namespace App\Http\Resources\Transfer;

use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'article'         => new ArticleResource($this->whenLoaded('article')),
            'article_id'      => $this->article_id,
            'transferred_by'  => new UserResource($this->whenLoaded('transferredBy')),
            'from_location'   => $this->from_location,
            'to_location'     => $this->to_location,
            'person_name'     => $this->person_name,
            'quantity'        => $this->quantity,
            'quantity_before' => $this->quantity_before,
            'quantity_after'  => $this->quantity_after,
            'notes'           => $this->notes,
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
