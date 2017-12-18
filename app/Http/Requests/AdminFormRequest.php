<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use Illuminate\Foundation\Http\FormRequest;

class AdminFormRequest extends FormRequest
{
    const CATEGORY_COMMON_RULES = [
        'alias' => 'required|unique:categories,alias|min:2|max:' . DBColumnLengthData::CATEGORIES_TABLE['alias'],
        'name' => 'required|unique:categories_locale,name|min:2|max:' . DBColumnLengthData::CATEGORIES_LOCALE_TABLE['name']
    ];

    const SUBCATEGORY_COMMON_RULES = [
        'alias' => 'required|unique:subcategories,alias|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_TABLE['alias'],
        'name' => 'required|unique:subcategories_locale,name|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_LOCAL_TABLE['name']
    ];

    const POST_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::POSTS_TABLE['alias'],
        'header' => 'required|min:2|max:' . DBColumnLengthData::POSTS_LOCALE_TABLE['header'],
        'text' => 'required|min:2|max:' . DBColumnLengthData::POSTS_LOCALE_TABLE['text']
    ];

    const POST_PARTS_COMMON_RULES = [
        'head' => 'required|min:2|max:' . DBColumnLengthData::POST_PARTS_TABLE['head'],
        'foot' => 'required|min:2|max:' . DBColumnLengthData::POST_PARTS_TABLE['foot']
    ];

    const HASHTAG_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::HASHTAG_TABLE['alias'],
        'hashtag' => 'required|min:2|max:' . DBColumnLengthData::HASHTAG_LOCALE_TABLE['hashtag']
    ];

    const REQUIRED = 'required';
    const MAX_10 = 'max:10';

    const REQUIRE_EXISTS = [
        'categories' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:categories,id'
        ],
        'categories_locale' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:categories_locale,id'
        ],
        'subcategories' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:subcategories,id'
        ],
        'subcategories_locale' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:subcategories_locale,id'
        ],
        'hashtags' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:hashtags,id'
        ],
        'hashtags_locale' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:hashtags_locale,id'
        ],
        'locale' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:locale,id'
        ],
        'posts' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:posts,id'
        ],
        'post_parts' => [
            'id' => self::REQUIRED . '|' . self::MAX_10 . '|exists:post_parts,id'
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        $validationResult['type'] = 'Validation Error';
        $validationResult['response'] = array_values($errors);
        return ResponseController::_validationResultResponse($validationResult);
    }
}
