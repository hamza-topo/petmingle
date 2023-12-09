<?php

namespace App\Filters;

use App\Models\Pet;
use Illuminate\Support\Collection;

class PetFilter
{
    public $pets;

    public function filter(Collection $pets, array $request = []): self
    {
        $this->pets =  $pets->filter(function ($localisation) use (&$request) {
            return $localisation->pet->age < $request['filters']['age']['max'] && $localisation->pet->age >= $request['filters'['age']]['min'] ||
                $localisation->pet->race->race_id == $request['filters']['race_id'] && $localisation->pet->color == $request['color'];
        });

        return $this;
    }

    public function get()
    {
        return $this->pets;
    }
}
