<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialsResource extends JsonResource
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
            'id'   		    => $this['id'],
            'name' 		    => $this['name'],
            'image' 	    => filter_var($this['image'], FILTER_VALIDATE_URL) ? $this['image'] : asset('/storage/media/' . $this['image']),
            'description' 	=> $this['description']
	    ];
    }
}
