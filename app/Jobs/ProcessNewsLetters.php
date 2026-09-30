<?php

namespace App\Jobs;

use App\Enums\NewsLetter;
use App\Mail\NewsLetterMail;
use App\Repositories\NewsLetterRepository;
use App\Repositories\PetRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessNewsLetters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        NewsLetterRepository $newsLetterRepository,
        PetRepository $petRepository
    ): void {
        $newsLetters = $newsLetterRepository->getByTypes([
            NewsLetter::ALL,
            NewsLetter::EMAIL,
        ]);

        foreach ($newsLetters as $newsLetter) {
            $pets = $newsLetter->species_id
                ? $petRepository->getBySpeciesId((int) $newsLetter->species_id)
                : $petRepository->all();

            $emails = $pets
                ->map(fn ($pet) => $pet->owner?->email)
                ->filter()
                ->unique();

            foreach ($emails as $email) {
                Mail::to($email)->queue(
                    new NewsLetterMail($newsLetter)
                );
            }
        }
    }
}