<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Cerbero\QueryFilters\FiltersRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, FiltersRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'whatsapp',
        'avatar',
        'type',
        'status',
        'cms',
        'about_us',
        'links',
        'titles',
        'logos'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status'            => 'boolean'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class,'user_roles')->withPivot('role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'user_roles')->withPivot('project_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function isAdmin()
    {
        if ($this->type == 'admin' && $this->is_active == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */

    protected function cms(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

   protected function  aboutUs(): Attribute
   {
	return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
   }

   protected function  links(): Attribute
   {
        return Attribute::make(
                get: fn ($value) => json_decode($value, true),
                set: fn ($value) => json_encode($value),
            );
   }

   protected function  titles(): Attribute
   {
        return Attribute::make(
                get: fn ($value) => json_decode($value, true),
                set: fn ($value) => json_encode($value),
            );
   }

   protected function  logos(): Attribute
   {
        return Attribute::make(
                get: fn ($value) => json_decode($value, true),
                set: fn ($value) => json_encode($value),
            );
   }

    public function projectsDonors()
    {
        return $this->belongsToMany(Project::class,'project_donors');
   }
}
