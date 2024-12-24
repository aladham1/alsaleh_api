<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Cerbero\QueryFilters\QueryFilters;


class IncomeFilters extends QueryFilters
{

	public function project($val)
     {
          $this->query->whereHas('project', function ($query) use ($val) {
               return $query->where('id', $val);
          });
     }

     public function from($val)
     {
          $this->query->where('paid_at', '>=', Carbon::parse($val)->startOfDay());
     }

     public function to($val)
     {
          $this->query->where('paid_at', '<=', Carbon::parse($val)->endOfDay());
     }

     public function priceFrom($val)
     {
         $this->query->where('total','>=',$val);
     }

     public function priceTo($val)
     {
         $this->query->where('total','<=', $val);
     }

     public function description($val)
     {
         $this->query->where('description', 'like', "%{$val}%");
     }


    public function payer($val)
     {
	     $this->query->where('paid_to', $val);
     }

    public function sortBy($val)
     {
          foreach($val as $item){
               if($item == 'asc-total'){
                    $this->query->orderBy('total','asc');
               }
               elseif($item == 'desc-total'){
                    $this->query->orderBy('total','desc');
               }
               elseif($item == 'desc-paid_at'){
                    $this->query->orderBy('paid_at','desc');
               }
               elseif($item == 'asc-paid_at'){
                    $this->query->orderBy('paid_at','asc');
               }
     	}
     }
}
