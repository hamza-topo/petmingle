<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class LangService
{

    public function set(string $locale)
    {
        App::setLocale($locale);
    }

    public function current(): string
    {
        return App::currentLocale();
    }
}
