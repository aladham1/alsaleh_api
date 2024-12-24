<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $images = [];

        if ($this->relationLoaded('media')) {
            $media = $this->media->groupBy('type');

            $images = $media->get('image', []);
        }

        return [
            'id'            => $this->id,
            'project_id'    => $this->project_id,
            'total'         => $this->total,
            'description'   => $this->description,
            'paid_at'       => $this->paid_at->format('Y-m-d'),
            'paid_to'       => $this->paid_to,
            'created_at'    => $this->created_at->format('Y-m-d'),
            'images'        => MediaResource::collection($images),
        ];
    }
}
