<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Cerbero\QueryFilters\QueryFilters;


class CommentFilters extends QueryFilters
{



     public function project($val)
     {
          $this->query->whereHas('project', function ($query) use ($val) {
               return $query->where('id', $val);
          });
     }
     public function status($val)
     {
          $this->query->where('status', $val);
     }
}
