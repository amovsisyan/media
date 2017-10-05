<?php

namespace App\Http\Controllers\Helpers\Validator;

use App\Http\Controllers\Controller;

abstract class AbstractValidator extends Controller
{
    protected static function _generateValidationErrorResponse($validator)
    {
        $errors = $validator->errors();
        $response = [];
        foreach ($errors->all() as $message) {
            $response[] = $message;
        }
        return [
            'error' => true,
            'type' => 'Validation Error',
            'response' => $response
        ];
    }

    protected static function _generateValidationOKResponse()
    {
        return [
            'error' => false,
        ];
    }
}
