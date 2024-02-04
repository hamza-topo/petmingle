<?php

namespace App\Services;

use App\Services\Contracts\Base;

class NewsLetterService implements Base
{


    public function process(): bool
    {
        return true;
    }

    public function send(): bool
    {
        
        return true;
    }

    public function bulk(): mixed
    {
        return [];
    }
}
