<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectBoxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $progress = $this->total_requested ? ceil(($this->total_paid / $this->total_requested) * 100) : 0;
        $images = [];
        if ($this->relationLoaded('media')) {
            $media = $this->media->groupBy('type');
            $images = $media->get('image', []);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'avatar' => asset('/storage/' . $this->avatar),
            'total_paid' => $this->total_paid,
            'total_requested' => $this->total_requested,
            'total_remaining' => max($this->total_requested - $this->total_paid, 0),
            'progress' => $progress > 100 ? 100 : $progress,
            'images' => MediaResource::collection($images),
            'expenses' => ExpenseResource::collection($this->whenLoaded('expenses')),
            'incomes' => IncomeResource::collection($this->whenLoaded('incomes')),
        ];
    }
}
