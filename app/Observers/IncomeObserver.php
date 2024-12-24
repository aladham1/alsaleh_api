<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     *
     * @param  \App\Models\Income  $income
     * @return void
     */
    public function created(Income $income)
    {

        $income->project->increment('total_paid', $income->total);
    }

    /**
     * Handle the Income "updated" event.
     *
     * @param  \App\Models\Income  $income
     * @return void
     */
    public function updating(Income $income)
    {
        $income->project->decrement('total_paid', $income->getOriginal('total'));

        $income->project->increment('total_paid', $income->total);
    }

    /**
     * Handle the Income "deleted" event.
     *
     * @param  \App\Models\Income  $income
     * @return void
     */
    public function deleted(Income $income)
    {
        $income->project->decrement('total_paid', $income->total);
    }


    /**
     * Handle the Income "force deleted" event.
     *
     * @param  \App\Models\Income  $income
     * @return void
     */
    public function forceDeleted(Income $income)
    {
        $income->project->decrement('total_paid', $income->total);
    }
}
