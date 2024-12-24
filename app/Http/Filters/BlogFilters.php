<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Cerbero\QueryFilters\QueryFilters;


class BlogFilters extends QueryFilters
{



     public function project($val)
     {
          $this->query->whereHas('project', function ($query) use ($val) {
               return $query->where('id', $val);
          });
     }

     public function from($val)
     {
          $this->query->where('created_at', '>=', Carbon::parse($val)->startOfDay());
     }

     public function to($val)
     {
          $this->query->where('created_at', '<=', Carbon::parse($val)->endOfDay());
     }
}
