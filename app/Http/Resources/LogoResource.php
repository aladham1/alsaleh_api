<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogoResource extends JsonResource
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
            'visitor_title' => $this['visitor_title'],
            'visitor_logo'  => filter_var($this['visitor_logo'], FILTER_VALIDATE_URL) ? $this['visitor_logo'] : asset('/storage/' . $this['visitor_logo']),
            'admin_logo'    => filter_var($this['admin_logo'], FILTER_VALIDATE_URL) ? $this['admin_logo'] : asset('/storage/' . $this['admin_logo']),
        ];
    }
}
