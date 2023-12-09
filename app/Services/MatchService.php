<?php

namespace App\Services;

use App\Events\MatchEvent;
use App\Mail\ItsAMatch;
use App\Models\MatchTable;
use App\Repositories\MatchRepository;
use Illuminate\Support\Facades\Mail;

class MatchService
{
    protected MatchTable $fromMatch;
    protected MatchTable $toMatch;

    public function __construct(
        protected MatchRepository $matchRepository,

    ) {
    }

    public function create(array $like): self
    {
        $likeSeconde = ['from' => $like['to'], 'to' => $like['to']];
        $this->fromMatch =  $this->matchRepository->create($like);
        $this->toMatch = $this->matchRepository->create($likeSeconde);

        return $this;
    }

    public function notify(): self
    {
        MatchEvent::dispatch($this->fromMatch, $this->toMatch);

        return $this;
    }

    public function mail(): void
    {
        Mail::to($this->fromMatch?->fromPet?->owner?->email)
            ->queue(new ItsAMatch($this->fromMatch->toPet));
        Mail::to($this->toMatch?->toPet?->owner?->email)
            ->queue(new ItsAMatch($this->fromMatch->fromPet));
    }
}
