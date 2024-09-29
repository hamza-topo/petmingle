<?php

namespace App\Observers;

use App\Models\Adoption;
use App\Repositories\AdoptionRepository;
use App\Services\AdoptionService;

class AdoptionObserver
{
    public function __construct(
        protected AdoptionRepository $adoptionRepository,
        protected AdoptionService $adoptionService
    ) {}
    /**
     * Handle the Adoption "created" event.
     *
     * @param  \App\Models\Adoption  $adoption
     * @return void
     */
    public function created(Adoption $adoption)
    {
        $this->adoptionService->setAdoption($adoption)
            ->notify()
            ->mail();
    }

    /**
     * Handle the Adoption "updated" event.
     *
     * @param  \App\Models\Adoption  $adoption
     * @return void
     */
    public function updated(Adoption $adoption)
    {
        //
    }

    /**
     * Handle the Adoption "deleted" event.
     *
     * @param  \App\Models\Adoption  $adoption
     * @return void
     */
    public function deleted(Adoption $adoption)
    {
        //
    }

    /**
     * Handle the Adoption "restored" event.
     *
     * @param  \App\Models\Adoption  $adoption
     * @return void
     */
    public function restored(Adoption $adoption)
    {
        //
    }

    /**
     * Handle the Adoption "force deleted" event.
     *
     * @param  \App\Models\Adoption  $adoption
     * @return void
     */
    public function forceDeleted(Adoption $adoption)
    {
        //
    }
}
