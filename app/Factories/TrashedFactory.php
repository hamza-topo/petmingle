<?php
namespace App\Factories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Queue;

class TrashedFactory
{

    // ila ban lik a shef  nkhedmo ghi b filter or builder ola ghi trait or other design pattern ... tell me nbdel babaha :)  
    /*
     * Apply the trashed scope to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $query
     * @param  bool  $withTrashed
     * @author Youssef tamri <yousseftam100@gmail.com>
     * 
     */

     // we can use it in all models has soft delete a shef but if no need u send it to hell :)
    public static function apply(Builder $query):Builder
    {
        return session()->get('show_trashed', false) ? $query->withTrashed() : $query;
    }
}
