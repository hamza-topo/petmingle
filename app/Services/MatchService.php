<?php

namespace App\Services;

use App\Events\MatchEvent;
use App\Mail\ItsAMatch;
use App\Models\MatchTable;
use App\Models\Pet;
use App\Repositories\MatchRepository;
use App\Repositories\PetRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MatchService
{
    protected MatchTable $fromMatch;
    protected MatchTable $toMatch;

    public function __construct(
        protected MatchRepository $matchRepository,
        protected PetRepository $petRepository
    ) {
    }

    public function create(array $like): self
    {
        Log::info('start creating the match');
        $likeSeconde = ['from' => $like['to'], 'to' => $like['from']];
        $this->fromMatch =  $this->matchRepository->create($like);
        Log::info('created from :' . json_encode($this->fromMatch));
        $this->toMatch = $this->matchRepository->create($likeSeconde);
        Log::info('created to :' . json_encode($this->toMatch));
        Log::info('end of creating the match');
        return $this;
    }

    public function notify(): self
    {
        MatchEvent::dispatch($this->fromMatch, $this->toMatch);

        return $this;
    }

    public function mail(): void
    {
        Log::info('start processing the mail');
        try {
            Log::info('Mail: the fromMatch: ' . json_encode($this->fromMatch));
            Mail::to($this->fromMatch->fromPet?->owner?->email)
                ->queue(new ItsAMatch($this->fromMatch->toPet, $this->fromMatch->fromPet));
            Mail::to($this->fromMatch->toPet?->owner?->email)
                ->queue(new ItsAMatch($this->fromMatch->fromPet, $this->fromMatch->toPet));
        } catch (\Exception $e) {
            Log::error('Ko : ' . $e->getMessage());
        }
        Log::info('end processing the mail');
    }
}
