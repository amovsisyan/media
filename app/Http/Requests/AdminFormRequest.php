<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use Illuminate\Foundation\Http\FormRequest;

class AdminFormRequest extends FormRequest
{
    const CATEGORY_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::CATEGORIES_TABLE['alias'],
        'name' => 'required|min:2|max:' . DBColumnLengthData::CATEGORIES_LOCALE_TABLE['name']
    ];

    const SUBCATEGORY_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_TABLE['alias'],
        'name' => 'required|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_LOCAL_TABLE['name']
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
