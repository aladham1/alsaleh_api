<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Cerbero\QueryFilters\QueryFilters;


class ActivityFilters extends QueryFilters
{



        public function name($val)
        {
            $this->query->whereRaw('lower(name) like lower(?)', ["%{$val}%"]);
        }
}
