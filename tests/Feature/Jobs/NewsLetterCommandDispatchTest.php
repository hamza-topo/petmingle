<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessNewsLetters;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsLetterCommandDispatchTest extends TestCase
{
    public function test_newsletter_command_dispatches_processing_job_to_queue(): void
    {
        Queue::fake();

        $exitCode = Artisan::call(
            'news-letter:emailing'
        );

        $this->assertSame(0, $exitCode);

        Queue::assertPushed(
            ProcessNewsLetters::class,
            1
        );

        $this->assertStringContainsString(
            'Job dispatched successfully.',
            Artisan::output()
        );
    }
}
