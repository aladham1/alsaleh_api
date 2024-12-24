<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
            'name'
        ];

    public function users(){
        return $this->belongsToMany(User::class,'user_roles')->withPivot('project_id');
    }

    public function projects(){
        return $this->belongsToMany(Project::class,'user_roles')->withPivot('user_id');
    }
}
