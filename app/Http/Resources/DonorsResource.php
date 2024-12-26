<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DonorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'avatar'         => asset('/storage/' . $this->avatar),
            'status'         => $this->status,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'whatsapp'       => $this->whatsapp,
            'created_at'     => $this->created_at->format('Y-m-d'),
            'projects' =>ProjectDonorResource::collection($this->whenLoaded('projectsDonors'))
        ];
    }
}