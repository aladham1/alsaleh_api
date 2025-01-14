<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use Auth;


class ManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $roles = [];
        $login = false;
        if ($this->relationLoaded('roles')) {
            if ($this->pivot) {
                $roles = $this->roles()->where('project_id', $this->pivot->project_id)->get();
            } else {
                $roles = [];
            }
        }

        if (Auth::check()) {
            if ($request->user()->isAdmin()) {
                $login = true;
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $roles,
            'avatar' => asset('/storage/' . $this->avatar),
            'status' => $this->status,
            'username' => $this->username,
            'password' => $this->when($login, Crypt::decryptString($this->password)),
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'created_at' => $this->created_at->format('Y-m-d'),
            'projects_count' => $this->whenCounted('projects')
        ];
    }
}
