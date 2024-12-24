<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cerbero\QueryFilters\FiltersRecords;

class UserActivity extends Model
{
    use HasFactory, FiltersRecords;

    protected $fillable = [
        'name',
        'activity',
        'url'
    ];
}
