<?php

namespace App\Observers;

use App\Enums\Species as EnumsSpecies;
use App\Models\Species;
use App\Services\CacheService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SpeciesObserver implements ShouldHandleEventsAfterCommit
{


    public function __construct(
        protected CacheService $cacheService
    ) {}

    public function created(Species $species): void
    {
        $this->clearCache();
    }

    public function updated(Species $species): void
    {
        $this->clearCache();
    }

    public function deleted(Species $species): void
    {
        $this->clearCache();
    }

    public function restored(Species $species): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Species $species): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        $this->cacheService->clear(EnumsSpecies::CACHEKEY);
    }
}
