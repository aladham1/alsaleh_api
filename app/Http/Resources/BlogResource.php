<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
        $videos = [];
        $users   = [];
        if ($this->relationLoaded('media')) {
            $media = $this->media->groupBy('type');

            $images = $media->get('image', []);
            $videos = $media->get('video', []);
        }

        if ($this->relationLoaded('user')) {
            $users = $this->user->where('id',$this->user_id)->get();
        }


        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'project_id' => $this->project_id,
            'content'    => $this->content,
            'created_at' => $this->created_at->format('Y-m-d'),
            'publisher'  => UserResource::collection($users)[0],
            'images'     => MediaResource::collection($images),
            'videos'     => MediaResource::collection($videos),
        ];
    }
}
