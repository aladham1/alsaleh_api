<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TestimonialsResource;
class CmsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
	  return [
		'projects' => [],
		'about_us' => $this['about_us'],
		'testimonials'=> TestimonialsResource::collection($this['testimonials']),

	];
    }
}
