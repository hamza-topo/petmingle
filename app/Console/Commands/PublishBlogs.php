<?php

namespace App\Console\Commands;

use App\Repositories\BlogRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PublishBlogs extends Command
{
    protected $signature = 'blogs:publish';

    protected $description = 'Publish scheduled blogs whose publication time has arrived';

    public function __construct(protected BlogRepository $blogRepository)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $drafts = $this->blogRepository->getDueForPublication(Carbon::now()->format('Y-m-d H:i:s'));
            $count = $this->blogRepository->publishBulk($drafts->pluck('id')->all());
            $this->info("Published {$count} blogs.");

            return self::SUCCESS;
        } catch (\Exception $e) {
            report($e);
            $this->error('Blog publication failed.');

            return self::FAILURE;
        }
    }
}
