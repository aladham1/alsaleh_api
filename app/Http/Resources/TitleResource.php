<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TitleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'projects' => $this['projects'],
            'about_us' => $this['about_us'],
            'archive'  => $this['archive']
        ];
    }
}
