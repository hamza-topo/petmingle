<?php

namespace App\Support;

use InvalidArgumentException;

final class FrontendOrigins
{
    /** @return list<string> */
    public static function parse(string $value, bool $production): array
    {
        $origins = array_values(array_unique(array_filter(array_map('trim', explode(',', $value)))));

        if ($production) {
            foreach ($origins as $origin) {
                $parts = parse_url($origin);
                if ($parts === false || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])
                    || isset($parts['user']) || isset($parts['pass'])
                    || isset($parts['path']) || isset($parts['query']) || isset($parts['fragment'])
                    || str_contains($origin, '*') || filter_var($origin, FILTER_VALIDATE_URL) === false) {
                    throw new InvalidArgumentException('CORS_ALLOWED_ORIGINS must contain exact HTTPS origins without paths, credentials or wildcards.');
                }
            }
        }

        return $origins;
    }
}
