<?php

namespace App\Jobs;

use App\Enums\NewsLetter;
use App\Repositories\NewsLetterRepository;
use App\Repositories\PetRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsLatter;

class ProcessNewsLetters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected NewsLetterRepository $newsLetterRepository;
    protected PetRepository $petRepository;

    /**
     * Create a new job instance.
     * @author Youssef tamri <yousseftam100@gmail.com>
     * @return void
     */
    public function __construct()
    {
        $this->newsLetterRepository = new NewsLetterRepository;
        $this->petRepository = new PetRepository;
    }

    public function handle()
    {
        //TODO:get the newsLetter where the type is for emailing or all and active
        $newsLetters = $this->newsLetterRepository->getByTypes([NewsLetter::ALL, NewsLetter::EMAIL]);

        foreach ($newsLetters as $newsLetter) {
            if (!empty($newsLetter)) {
                $pets = $newsLetter->species_id 
                    ? $this->petRepository->getById($newsLetter->species_id) 
                    : $this->petRepository->all();

                    foreach ($pets as $pet) {
                        // Send email to the pet owner
                        Mail::to($pet->owner()->email)->queue(new NewsLatter($newsLetter));
                    }
            }
        }

        //TODO:loop each newsLetter and get the included species for 
        //TODO:list all users that has same pet species
        //TODO:run bulk send Mailing process
        //TODO:fetch all 
    }
}
