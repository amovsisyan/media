<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DBColumnLengthData extends Controller
{
    const CATEGORIES_TABLE = [
        'alias' => 30,
        'name' => 30
    ];

    const SUBCATEGORIES_TABLE = [
        'alias' => 30,
        'name' => 30
    ];

    const POSTS_TABLE = [
        'alias' => 30,
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
        'alias' => 40,
        'hashtag' => 40
    ];
}
