<?php

namespace App\Enums;

class Header extends Enum
{
    /**
     * const default number of days
     */
    const  MENUS = [
        'Home' => [
            'url' => '',
            'Text' => 'Home',
            'show' => true,
        ],
        'About' => [
            'url' => '/about',
            'Text' => 'About',
            'show' => true,
        ],
        'Dating' => [
            'url' => '/search?type=0',
            'Text' => 'Dating',
            'show' => true,
        ],
        'Pet-Sitters' => [
            'url' => '/search?type=1',
            'Text' => 'Pet-Sitters',
            'show' => true,
        ],
        'Adoption' => [
            'url' => '/search?type=2',
            'Text' => 'Adoption',
            'show' => true,
        ],
        'Blogs' => [
            'url' => '/blogs',
            'Text' => 'Blogs',
            'show' => true,
        ],
        'Contact' => [
            'url' => '/contact',
            'Text' => 'Contact',
            'show' => true,
        ],
    ];
}
