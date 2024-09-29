<?php

namespace APP\Services;

use App\Events\AdoptionEvent;
use App\Mail\ItsAdoption;
use App\Models\Adoption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdoptionService
{

    protected Adoption $adoption;

    public function setAdoption(Adoption $adoption): self
    {
        $this->adoption = $adoption;

        return $this;
    }

    public function notify()
    {
        AdoptionEvent::dispatch($this->adoption);

        return $this;
    }

    public function mail()
    {
        Log::info('start processing the mail Adoption');
        try {
            Log::info('Mail: the Adoption: ' . json_encode($this->adoption));
            Mail::to($this->adoption->owner?->email)
                ->queue(new ItsAdoption($this->adoption->pet, $this->adoption->owner, $this->adoption->newOwner));
            Mail::to($this->adoption->newOwner?->email)
                ->queue(new ItsAdoption($this->adoption->pet, $this->adoption->newOwner, $this->adoption->owner));
        } catch (\Exception $e) {
            Log::error('Ko : ' . $e->getMessage());
        }
        Log::info('end processing the mail: Adoption');
    }
}
