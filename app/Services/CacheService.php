<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function clear(string $key): bool
    {
        return Cache::forget($key);
    }

    public function remember(string $key, int $minutes, callable $callback): mixed
    {
        return Cache::remember($key, $minutes, $callback);
    }
}
