<?php

namespace App\Enums;

enum Component: string
{
    /**
     * const default number of days
     */
    case ABOUT = 'c-about';
    case BLOG = 'c-blog';
    case BRAND = 'c-brand';
    case HERO = 'c-hero';
    case PACKAGE = 'c-package';
    case PLAN = 'c-plan';
    case PRICING = 'c-pricing';
    case PROMOTION = 'c-promotion';
    case SPECIAL = 'c-special';
    case TESTIMONIAL = 'c-testimonial';

    public static function reverseMatch(string $value): ?Component
    {
        return match ($value) {
            'c-about' => Component::ABOUT,
            'c-blog' => Component::BLOG,
            'c-brand' => Component::BRAND,
            'c-hero' => Component::HERO,
            'c-package' => Component::PACKAGE,
            'c-plan' => Component::PLAN,
            'c-pricing' => Component::PRICING,
            'c-promotion' => Component::PROMOTION,
            'c-special' => Component::SPECIAL,
            'c-testimonial' => Component::TESTIMONIAL,
            default => null, // Fallback for invalid values
        };
    }
}
