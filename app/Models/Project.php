<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cerbero\QueryFilters\FiltersRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 *
 */
class Project extends Model
{
    use HasFactory, FiltersRecords, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'avatar',
        'total_paid',
        'total_requested',
        'min_donation_fee',
        'increment_by',
        'bank_name',
        'bank_branch',
        'bank_iban',
        'country',
        'city',
        'gov',
        'lat',
        'lng',
        'status',
        'created_at',
        'is_public',
        'category_id',
        'whatsapp',
	'in_home'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'total_paid' => 'float',
        'total_requested' => 'float',
        'min_donation_fee' => 'float',
        'increment_by' => 'float',
        'lat' => 'double',
        'lng' => 'double',
        'is_public' => 'boolean',
    ];

    protected $appends = ['category_name'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_roles')->withPivot('role_id');
    }

    /**
     * @return BelongsToMany
     */
    public function roles(){
        return $this->belongsToMany(Role::class,'user_roles')->withPivot('project_id');
    }

    /**
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * @return HasMany
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    /**
     * @return BelongsToMany
     */
    public function donors(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'project_donors');
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function getCategoryNameAttribute()
    {
        return $this->category?->name;
    }
}
