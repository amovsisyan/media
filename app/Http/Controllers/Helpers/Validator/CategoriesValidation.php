<?php

namespace App\Http\Controllers\Helpers\Validator;

use App\Http\Controllers\Data\DBColumnLengthData;
use Validator;

class CategoriesValidation extends AbstractValidator
{
    const CATEGORY_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::CATEGORIES_TABLE['alias'],
        'name' => 'required|min:2|max:' . DBColumnLengthData::CATEGORIES_LOCALE_TABLE['name']
    ];

    const SUBCATEGORY_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_TABLE['alias'],
        'name' => 'required|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_LOCAL_TABLE['name']
    ];

    public static function validateCategoryCreate($allRequest)
    {
        // todo standardizatoin needed 1-2 -->1
        foreach ($allRequest['categories_names'] as $key => $value) {
            $idValidation = "categories_names." . $key . '.locale_id';
            $nameValidation = "categories_names." . $key . '.name';
            $rules[$idValidation] = 'required';
            $rules[$nameValidation] = self::CATEGORY_COMMON_RULES['name'];
        };

        $rules['category_alias'] = self::CATEGORY_COMMON_RULES['alias'];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateCategoryDelete($validateData)
    {
        foreach ($validateData as $key => $value) {
            $idValidation = $key;
            $rules[$idValidation] = 'required';
        };

        $validator = Validator::make($validateData, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditCategorySearchValues($allRequest)
    {
        $rules = [
            'searchType' => 'required',
            'searchText' => 'required'
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditCategorySearchValuesSave($allRequest)
    {
        $rules = [
            'catId' => 'required',
            'catAlias' => self::CATEGORY_COMMON_RULES['alias'],
        ];

        // todo standardizatoin needed  1-2 -->1
        foreach ($allRequest['localesInfo'] as $key => $value) {
            $idValidation = "localesInfo." . $key . '.locale_id';
            $nameValidation = "localesInfo." . $key . '.name';
            $rules[$idValidation] = 'required';
            $rules[$nameValidation] = self::CATEGORY_COMMON_RULES['name'];
        };

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateSubcategoryCreate($allRequest)
    {
        $rules = [
            'subcategoryAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'categoryId' => 'required|integer'
        ];
        // todo standardizatoin needed  1-2 -->3
        foreach ($allRequest['subcategoryNames'] as $key => $value) {
            $idValidation = "subcategoryNames." . $key . '.locale_id';
            $nameValidation = "subcategoryNames." . $key . '.name';
            $rules[$idValidation] = 'required';
            $rules[$nameValidation] = self::SUBCATEGORY_COMMON_RULES['name'];
        };


        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateSubcategoryDelete($allRequest)
    {
        $rules = [
            'subcategoryId' => 'required|min:1|max:10'
        ];
        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditSubcategorySearchValues($allRequest) {
        $rules = [
            'searchType' => 'required',
            'searchText' => 'required'
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditSubcategorySearchValuesSave($allRequest) {
        $rules = [
            'id' => 'required|min:1|max:10',
            'newCategoryId' => 'required|min:1|max:10',
            'newAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
        ];

        foreach ($allRequest['subcategoryNames'] as $key => $name) {
            $idValidation = "subcategoryNames." . $key . '.id';
            $nameValidation = "subcategoryNames." . $key . '.name';
            $rules[$idValidation] = 'required|min:1|max:10';
            $rules[$nameValidation] = self::SUBCATEGORY_COMMON_RULES['name'];
        }

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }
}
