<?php

namespace Tests\Unit\Observers;

use App\Models\Like;
use App\Observers\LikeObserver;
use App\Repositories\LikeRepository;
use App\Services\MatchService;
use RuntimeException;
use Tests\TestCase;

class LikeObserverTest extends TestCase
{
    public function test_mail_is_not_triggered_when_match_creation_fails(): void
    {
        $like = new Like([
            'from' => 1,
            'to' => 2,
        ]);

        $likeRepository = $this->mock(LikeRepository::class);
        $matchService = $this->mock(MatchService::class);

        $likeRepository
            ->shouldReceive('isMatch')
            ->once()
            ->andReturnTrue();

        $matchService
            ->shouldReceive('create')
            ->once()
            ->andThrow(new RuntimeException('Match creation failed.'));

        $matchService
            ->shouldNotReceive('mail');

        $observer = new LikeObserver(
            $likeRepository,
            $matchService
        );

        $observer->created($like);

        $this->assertTrue(true);
    }

    public function test_mail_is_triggered_after_successful_match_creation(): void
    {
        $like = new Like([
            'from' => 1,
            'to' => 2,
        ]);

        $likeRepository = $this->mock(LikeRepository::class);
        $matchService = $this->mock(MatchService::class);

        $likeRepository
            ->shouldReceive('isMatch')
            ->once()
            ->andReturnTrue();

        $matchService
            ->shouldReceive('create')
            ->once()
            ->with($like->toArray())
            ->andReturn($matchService);

        $matchService
            ->shouldReceive('mail')
            ->once();

        $observer = new LikeObserver(
            $likeRepository,
            $matchService
        );

        $observer->created($like);
    }
}