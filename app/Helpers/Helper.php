<?php

use App\Enums\App;
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
