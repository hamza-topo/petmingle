<?php 
namespace App\Enums;

enum User {

    const ACCOUNT_VERIFIED = 1;
    const ACCOUNT_NOT_VERIFIED = 0;
    const PAGINATE = 5 ;
    const CACHEKEY = 'all_users' ;

    const ADMIN = 1;
    const NON_ADMIN = 0;

    /**
     * Get the label for the given admin status.
     *
     * @param bool $status
     * @return string
     */
    public static function options(): array
    {
        return [
            self::ADMIN => __('Admin'),
            self::NON_ADMIN => __('Regular User'),
        ];
    }

    public static function verificationOptions(): array
    {
        return [
            self::ACCOUNT_VERIFIED => __('Verified'),
            self::ACCOUNT_NOT_VERIFIED => __('Not Verified'),
        ];
    }

     
}