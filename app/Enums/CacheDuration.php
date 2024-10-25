<?php

namespace App\Enums;

enum CacheDuration: int
{
    case SHORT = 60;       // 1 minute
    case MEDIUM = 3600;    // 1 hour
    case LONG = 86400;     // 1 day
}
