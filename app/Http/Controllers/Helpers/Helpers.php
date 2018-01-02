<?php

namespace App\Http\Controllers\Helpers;

use App\AdminNavbar;
use App\AdminNavbarParts;
use App\CategoryLocale;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Locale\LocaleSettings;

class Helpers extends Controller
{
    // ToDo Should here add CACHE part logic. Change , when PC will be 64 ))
    public static function prepareAdminNavbars()
    {
        $response = [];
        $part = self::getSegmentForAdminNavbar();
        $response['leftNav'] = AdminNavbar::prepareLeftNavbar();
        $response['panel'] = AdminNavbarParts::preparePanelNavbar($part);
        return $response;
    }

    /**
     * get Navbar for Front
     * @return array
     */
    public static function getNavbar ()
    {
        $categoriesLocale = CategoryLocale::getCategorySubcategoryLocalized();

        $response = [];
        foreach ($categoriesLocale as $key => $categoryLocale) {
            $response[$key]['category'] = [
                'alias' => $categoryLocale['category']->alias
                , 'name'  => $categoryLocale->name,
            ];

            $subcategories = $categoryLocale['category']['subcategories'];

            foreach ($subcategories as $subcategory) {
                $subcategoryLocale = $subcategory['subcategoriesLocale'];

                foreach ($subcategoryLocale as $localeSub) {
                    $response[$key]['subcategory'][] = [
                        'id' => $localeSub->id
                        , 'alias' => $subcategory->alias
                        , 'name' => $localeSub->name
                    ];
                }
            }
        }
        $response['activeLocales'] = LocaleSettings::getActiveLocales();

        return  $response;
    }

    public static function getLocaleIdFromSession()
    {
        return session()->get('localeId', 1);
    }

    /**
     * Admin navbar segment for preparing Admin Navbar
     * @return null|string
     */
    public static function getSegmentForAdminNavbar()
    {
        return request()->segment(4);
    }

    public static function cleanToOnlyLettersNumbers($string)
    {
        $cleanCode = preg_replace('/[^a-zA-Z0-9]/', '', $string);
        return strtolower($cleanCode);
    }
}
