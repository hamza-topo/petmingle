<?php

namespace App\Console\Commands;

use App\Repositories\BlogRepository;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PublishBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogs:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected BlogRepository $blogRepository)
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
        try {
            $now = Carbon::now();
            // Clone the $now instance before modifying it
            $nowPlusTen = (clone $now)->addMinutes(10);
            $drafts = $this->blogRepository->getScheduledFor([$now->format('Y-m-d H:i:s'), $nowPlusTen->format('Y-m-d H:i:s')]);
            return $this->blogRepository->publishBulk($drafts->map(function ($row) {
                return $row->id;
            })->toArray());
        } catch (\Exception $e) {
            //throw $th;
        }
    }
}
