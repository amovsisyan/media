<?php

namespace App\Http\Controllers\Services\Locale;


use App\Http\Controllers\Controller;
use App\Locale;

class LocaleSettings extends Controller
{
    const DEFAULT_LOCALE = self::createArr['en']['name'];
    const DEFAULT_LOCALE_ID = 1;

    protected static $activeLocales = null;

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

    public static function getActiveLocales()
    {
        $activeLocales = self::$activeLocales ? : self::getActiveLocalesFromDB();

        $response = [];
        foreach ($activeLocales as $activeLocale) {
            $response[] = [
                'id' => $activeLocale->id
                , 'name' => $activeLocale->name
            ];
        }
        return $response;
    }

    protected static function getActiveLocalesFromDB()
    {
        $activeLocales = Locale::where('active', 1)->get();
        self::$activeLocales = $activeLocales;
        return $activeLocales;
    }
}
