<?php

namespace App\Observers;

use App\Enums\Species as EnumsSpecies;
use App\Models\Species;
use App\Services\CacheService;

class SpeciesObserver
{
   

    public function __construct(protected CacheService $cacheService)
    {
        $this->cacheService->clear(EnumsSpecies::CACHEKEY);
    }

    public function created(Species $specie)
    {
       
    }

    public function updated(Species $specie)
    {
       
    }

    public function deleted(Species $specie)
    {
      
    }

    public function restored(Species $specie)
    {
       
    }

    public function forceDeleted(Species $specie)
    {
        
    }
}
