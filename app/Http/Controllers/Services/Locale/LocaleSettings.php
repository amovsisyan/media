<?php

namespace App\Http\Controllers\Services\Locale;


use App\Http\Controllers\Controller;

class LocaleSettings extends Controller
{
    const DEFAULT_LOCALE = self::createArr['en']['name'];
    const DEFAULT_LOCALE_ID = 1;

    const createArr = [
        'en' => [
            'id' => 1,
            'name' => 'en'
        ],
        'ru' => [
            'id' => 2,
            'name' => 'ru'
        ],
        'am' => [
            'id' => 3,
            'name' => 'am'
        ]
    ];

    public static function getDefaultLocale()
    {
        return self::DEFAULT_LOCALE;
    }

    public static function getDefaultLocaleID()
    {
        return self::DEFAULT_LOCALE_ID;
    }
}
