<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    /**
     * Method to pluck given peroperty
     *
     * @param Collection $colletion
     * @param string $key
     * @return Collection
     */
    protected function plucker(Collection $colletion, string $key, string $value = ''): Collection
    {
        return $colletion->pluck($key, 'id')->prepend('Please select an: '. $value, '');
    }
}
