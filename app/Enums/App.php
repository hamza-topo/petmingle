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
    const PAGINATE = 25;

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
     * Currency
     */
    const CURRENCY = 'USD';

    /* 
     The `LOCALES` constant in the `App` enum class is defining an array of locales with their
     corresponding language codes. Each entry in the array consists of a key-value pair where
     the key represents the language code and the value represents the corresponding locale. In
     this case, the array contains three entries for English, Spanish, and French locales with
     their respective language codes 'EN', 'ES', and 'FR'. This constant can be used to easily
     reference and work with different locales in the application. 
    */
    const  LOCALES = [
        'EN' => 'en',
        'ES' => 'es',
        'FR' => 'fr',
    ];
}
