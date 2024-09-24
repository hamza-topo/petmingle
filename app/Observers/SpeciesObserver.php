<?php

namespace App\Observers;

use App\Models\Species;
use App\Services\CacheService;

class SpeciesObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function created(Species $specie)
    {
        $this->cacheService->clear('all_species');
    }

    public function updated(Species $specie)
    {
        $this->cacheService->clear('all_species');
    }

    public function deleted(Species $specie)
    {
        $this->cacheService->clear('all_species');
    }

    public function restored(Species $specie)
    {
        $this->cacheService->clear('all_species');
    }

    public function forceDeleted(Species $specie)
    {
        $this->cacheService->clear('all_species');
    }
}
