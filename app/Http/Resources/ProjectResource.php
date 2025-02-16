<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {

        $images = [];
        $videos = [];
        $super_manager = [];
        $general_manager = [];
        $media_manager = [];
        $financial_manager = [];
        if ($this->relationLoaded('media')) {
            $media = $this->media->groupBy('type');

            $images = $media->get('image', []);
            $videos = $media->get('video', []);
        }

        $progress = $this->total_requested ? ceil(($this->total_paid / $this->total_requested) * 100) : 0;

        if ($this->relationLoaded('managers')) {
            $super_manager = $this->managers()->where('role_id', 4)->first();
            $general_manager = $this->managers()->where('role_id', 5)->first();
            $financial_manager = $this->managers()->where('role_id', 6)->first();
            $media_manager = $this->managers()->where('role_id', 7)->first();
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'category' => $this->category,
            'slug' => $this->slug,
            'is_public' => $this->is_public,
            'whatsapp' => $this->whatsapp,
            'category_name' => $this->category_name,
            'description' => $this->description,
            'avatar' => asset('/storage/' . $this->avatar),
            'total_paid' => $this->total_paid,
            'total_requested' => $this->total_requested,
            'total_remaining' => max($this->total_requested - $this->total_paid, 0),
            'progress' => $progress > 100 ? 100 : $progress,
            'min_donation_fee' => $this->min_donation_fee,
            'increment_by' => $this->increment_by,
            'bank_name' => $this->bank_name,
            'bank_branch' => $this->bank_branch,
            'bank_iban' => $this->bank_iban,
            'country' => $this->country,
            'city' => $this->city,
            'gov' => $this->gov,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'images' => MediaResource::collection($images),
            'videos' => MediaResource::collection($videos),
            'managers' => ManagerResource::collection($this->whenLoaded('managers')),
            'super_manager' => new ManagerResource($super_manager),
            'general_manager' => new ManagerResource($general_manager),
            'financial_manager' => new ManagerResource($financial_manager),
            'media_manager' => new ManagerResource($media_manager),
            'expenses' => ExpenseResource::collection($this->whenLoaded('expenses')),
            'incomes' => IncomeResource::collection($this->whenLoaded('incomes')),
            'donors' => DonorsResource::collection($this->whenLoaded('donors')),
            'in_home' => $this->in_home,
            'total_incomes' => $this->incomes,
            'total_expenses' => $this->expenses
        ];
    }
}
