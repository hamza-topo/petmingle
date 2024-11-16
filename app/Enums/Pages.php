<?php

namespace App\Enums;

/**
 * Enum for pages
 */
enum Pages: string
{
    case HOME = 'HOME';
    case ABOUT = 'ABOUT';
    case CONTACT = 'CONTACT';
    case BLOGS = 'BLOGS';
    case ENGINE = 'ENGINE';
    case PRIVACY_POLICY = 'PRIVACY-POLICY';
    case FAQ = 'FAQ';
}
