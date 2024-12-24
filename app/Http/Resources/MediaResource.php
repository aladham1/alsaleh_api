<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'path' => filter_var($this->path, FILTER_VALIDATE_URL) ? $this->path : asset('/storage/media/' . $this->path),
            'type' => $this->type,
        ];
    }
}
