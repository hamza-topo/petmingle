<?php

namespace Tests\Unit\Observers;

use App\Models\Adoption;
use App\Observers\AdoptionObserver;
use App\Services\AdoptionService;
use Tests\TestCase;

class AdoptionObserverTest extends TestCase
{
    public function test_created_adoption_triggers_notification_and_mail(): void
    {
        $adoption = new Adoption([
            'from' => 1,
            'pet_id' => 2,
            'to' => 3,
        ]);

        $adoptionService = $this->mock(AdoptionService::class);

        $adoptionService
            ->shouldReceive('setAdoption')
            ->once()
            ->with($adoption)
            ->andReturnSelf();

        $adoptionService
            ->shouldReceive('notify')
            ->once()
            ->andReturnSelf();

        $adoptionService
            ->shouldReceive('mail')
            ->once();

        $observer = new AdoptionObserver($adoptionService);

        $observer->created($adoption);
    }
}