<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNewsLetters;
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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
 
    public function handle()
    {
        // Dispatch the job to process newsletters and send emails
        ProcessNewsLetters::dispatch();

        // display message about job execution process 
        $this->info('Job dispatched successfully.');
        return 0;
    }
}
