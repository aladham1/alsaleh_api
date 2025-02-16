<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Cerbero\QueryFilters\QueryFilters;


class ProjectFilters extends QueryFilters
{

     public function name($val)
     {
          $this->query->whereRaw('lower(name) like lower(?)', ["%{$val}%"])->orWhereHas('managers', function ($query) use ($val) {
               $query->whereRaw('lower(name) like lower(?)', ["%{$val}%"]);
          });
     }


     public function status($val)
     {
          $this->query->where('status', $val);
     }

    public function categoryId($val)
    {
        $this->query->where('category_id', $val);
    }


     public function from($val)
     {
          $this->query->where('created_at', '>=', Carbon::parse($val)->startOfDay());
     }

     public function public($val)
     {
          $this->query->where('is_public', $val);
     }

     public function to($val)
     {
          $this->query->where('created_at', '<=', Carbon::parse($val)->endOfDay());
     }

     public function sortBy($val)
     {
	     $this->query->orderBy('in_home','desc');
     }

     public function inHome($val)
     {
          if($val == 'true'){$in = 1;}
          else{$in = 0;}
          $this->query->where('in_home',$in);
     }
}
