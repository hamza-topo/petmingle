<?php

namespace App\Observers;

use App\Models\Adoption;
use App\Services\AdoptionService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class AdoptionObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(
        protected AdoptionService $adoptionService
    ) {}

    /**
     * Handle the Adoption "created" event.
     */
    public function created(Adoption $adoption): void
    {
        $this->adoptionService
            ->setAdoption($adoption)
            ->notify()
            ->mail();
    }

    /**
     * Handle the Adoption "updated" event.
     *
     * @return void
     */
    public function updated(Adoption $adoption)
    {
        //
    }

    /**
     * Handle the Adoption "deleted" event.
     *
     * @return void
     */
    public function deleted(Adoption $adoption)
    {
        //
    }

    /**
     * Handle the Adoption "restored" event.
     *
     * @return void
     */
    public function restored(Adoption $adoption)
    {
        //
    }

    /**
     * Handle the Adoption "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Adoption $adoption)
    {
        //
    }
}
