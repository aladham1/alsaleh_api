<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Cerbero\QueryFilters\QueryFilters;


class ManagerFilters extends QueryFilters
{

     public function name($val)
     {
          $this->query->whereRaw('lower(name) like lower(?)', ["%{$val}%"]);
     }

     public function status($val)
     {
          $this->query->where('status', $val);
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
