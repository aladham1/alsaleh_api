<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'project_id' => $this->project_id,
            'content' => $this->content,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_at_human' => $this->created_at->locale('ar')->diffForHumans(),
            'user' => UserResource::make($this->whenLoaded('user'))
        ];
    }
}
