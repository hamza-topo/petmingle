<?php

namespace Tests\Feature\Jobs;

use App\Models\Blog;
use App\Models\User;
use App\Repositories\BlogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PublishBlogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_due_blogs_are_published_and_success_returns_zero(): void
    {
        $this->travelTo(now()->startOfSecond());
        $user = User::withoutEvents(fn () => User::factory()->create());
        $attributes = ['user_id' => $user->id, 'title' => ['en' => 'Test'], 'slug' => ['en' => 'test'], 'content' => ['en' => 'Body'], 'active' => false];
        $due = Blog::create($attributes + ['publish_it_at' => now()->subHour()]);
        $future = Blog::create($attributes + ['publish_it_at' => now()->addMinutes(5)]);
        $draft = Blog::create($attributes);

        $this->artisan('blogs:publish')->expectsOutput('Published 1 blogs.')->assertExitCode(0);
        $this->assertEquals(1, $due->fresh()->active);
        $this->assertNull($due->fresh()->publish_it_at);
        $this->assertEquals(0, $future->fresh()->active);
        $this->assertEquals(0, $draft->fresh()->active);
        $this->travelBack();
    }

    public function test_publication_failure_returns_a_nonzero_exit_code(): void
    {
        $repository = Mockery::mock(BlogRepository::class);
        $repository->shouldReceive('getDueForPublication')->once()->andThrow(new \RuntimeException('Database unavailable'));
        $this->app->instance(BlogRepository::class, $repository);

        $this->artisan('blogs:publish')->expectsOutput('Blog publication failed.')->assertExitCode(1);
    }
}
