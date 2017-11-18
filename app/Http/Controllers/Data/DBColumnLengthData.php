<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;

class DBColumnLengthData extends Controller
{
    const LOCALE = [
        'name' => 2
    ];

    const CATEGORIES_TABLE = [
        'alias' => 30
    ];

    const CATEGORIES_LOCALE_TABLE = [
        'name' => 30
    ];

    const SUBCATEGORIES_TABLE = [
        'alias' => 30
    ];

    const SUBCATEGORIES_LOCAL_TABLE = [
        'name' => 30
    ];

    const POSTS_TABLE = [
        'alias' => 30
    ];

    const POSTS_LOCALE_TABLE = [
        'header' => 60,
        'text' => 80,
        'image' => 35
    ];

    const POST_PARTS_TABLE = [
        'head' => 500,
        'body' => 300,
        'foot' => 500
    ];

    const HASHTAG_TABLE = [
        'alias' => 40
    ];

    const HASHTAG_LOCALE_TABLE = [
        'hashtag' => 40
    ];

    public static function getCategoryLenghts()
    {
        return [
            'alias' => self::CATEGORIES_TABLE['alias'],
            'name' => self::CATEGORIES_LOCALE_TABLE['name']
        ];
    }

    public static function getSubCategoryLenghts()
    {
        return [
            'alias' => self::SUBCATEGORIES_TABLE['alias'],
            'name' => self::SUBCATEGORIES_LOCAL_TABLE['name']
        ];
    }

    public static function getHashtagLenghts()
    {
        return [
            'alias' => self::HASHTAG_TABLE['alias'],
            'hashtag' => self::HASHTAG_LOCALE_TABLE['hashtag']
        ];
    }

    public static function getPostLenghts()
    {
        return [
            'alias' => self::POSTS_TABLE['alias'],
            'header' => self::POSTS_LOCALE_TABLE['header'],
            'text' => self::POSTS_LOCALE_TABLE['text'],
            'image' => self::POSTS_LOCALE_TABLE['image']
        ];
    }

    public static function getPostPartsLenght()
    {
        return [
            'head' => self::POST_PARTS_TABLE['head'],
            'body' => self::POST_PARTS_TABLE['body'],
            'foot' => self::POST_PARTS_TABLE['foot']
        ];
    }
}
