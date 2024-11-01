<?php

use App\Enums\App;
use App\Enums\Pet;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

if (!function_exists('isNew')) {

    function isNew($createdAt): bool
    {
        return Carbon::now()->diffInDays(Carbon::parse($createdAt)) < App::NUMBER_OF_DAYS;
    }
}

if (!function_exists('displayHumanDate')) {

    function displayHumanDate(string $date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('sexIcon')) {

    function sexIcon(int $sex): string
    {
        return $sex === Pet::MALE ? 'ri-men-line' : 'ri-women-line';
    }
}

if (!function_exists('fakeImages')) {
    function fakeImages(array $images, int $position = 0): string
    {
        if (empty($images)) {
            return '';
        }

        // $result = file_get_contents($images[0]);
        return '';
        return json_decode($result)->message ?? '';
    }
}

if (!function_exists('lastActivity')) {
    function lastActivity(int $time = 0)
    {
        $last = $time != 0 ? Carbon::createFromTimestamp($time) : Carbon::now();

        return $last->diffForHumans();
    }
}

if (!function_exists('slugify')) {
    function slugify(string $word): string
    {
        return Str::slug($word);
    }
}
