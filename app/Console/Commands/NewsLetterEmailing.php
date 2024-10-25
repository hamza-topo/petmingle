<?php

namespace App\Console\Commands;

use App\Enums\NewsLetter;
use App\Repositories\NewsLetterRepository;
use App\Repositories\PetRepository;
use Illuminate\Console\Command;

class NewsLetterEmailing extends Command
{
    protected $newsLetterRepository;

    protected $petRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news-letter:emailing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'NewsLetters for mail campaigns';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->newsLetterRepository = new NewsLetterRepository;
        $this->petRepository = new PetRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //TODO:get the newsLetter where the type is for emailing or all and active
        $newsLetters = $this->newsLetterRepository->getByTypes([NewsLetter::ALL, NewsLetter::EMAIL]);
        $newsLetters->map(function ($newsLetter) {
            if (!empty($newsLetter->species_id)) {
                $pets = $this->petRepository->getBySpeciesId($newsLetter);
                foreach ($pets as $pet) {
                    //process the send with newsLetter content
                }
            }
            //get the users where they pets are in $newsLetter->species_id
            //TODO:handle it if we want all the species to be mailed

        });
        //TODO:loop each newsLetter and get the included species for 
        //TODO:list all users that has same pet species
        //TODO:run bulk send Mailing process
        //TODO:fetch all 
        return 0;
    }
}
