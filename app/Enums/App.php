<?php

namespace App\Enums;

enum App
{
    /**
     * const default number of days
     */
    const NUMBER_OF_DAYS = 30;

    /**
     * const Default pagination
     */
    const PAGINATE = 5;

    /**
     * const default order 
     */
    const ORDER = 'desc';

    /**
     * const max age in months
     */
    const MIN_AGE = 1;

    /**
     * const int max age in months
     */
    const MAX_AGE = 30;

    /**
     * const array locales
     */
    const LOCALES = [
        'EN' => 'en',
        'ES' => 'es',
        'FR' => 'fr',
    ];
}
