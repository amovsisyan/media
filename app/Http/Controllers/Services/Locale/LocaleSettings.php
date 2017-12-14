<?php

namespace App\Http\Controllers\Services\Locale;


use App\Http\Controllers\Controller;
use App\Locale;

class LocaleSettings extends Controller
{
    const DEFAULT_LOCALE = self::createArr['ru']['name'];
    const DEFAULT_LOCALE_ID = 2;

    protected static $activeLocales = null;

    const EN_ID = 1;
    const EN_NAME = 'en';

    const RU_ID = 2;
    const RU_NAME = 'ru';

    const AM_ID = 3;
    const AM_NAME = 'am';

    const createArr = [
        self::EN_NAME => [
            'id' => self::EN_ID,
            'name' => self::EN_NAME
        ],
        self::RU_NAME => [
            'id' => self::RU_ID,
            'name' => self::RU_NAME
        ],
        self::AM_NAME => [
            'id' => self::AM_ID,
            'name' => self::AM_NAME
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

    public static function getLocaleNameById($id)
    {
        foreach (self::createArr as $locale) {
            if($locale['id'] === $id) {
                return $locale['name'];
                break;
            }
        }
        return self::DEFAULT_LOCALE;
    }

    public static function getLocaleIdByName($name)
    {
        return self::createArr[$name]['id'] ? : null;
    }

    protected static function getActiveLocalesFromDB()
    {
        $activeLocales = Locale::where('active', 1)->get();
        self::$activeLocales = $activeLocales;
        return $activeLocales;
    }
}
