<?php

namespace App\Http\Controllers\Admin\Response;

use App\Http\Controllers\Controller;

class ResponseController extends Controller
{
    public static function _validationResultResponse($validationResult) {
        return response(
            [
                'error' => true,
                'type' => $validationResult['type'],
                'response' => $validationResult['response']
            ], 404
        );
    }

    public static function _catchedResponse(\Exception $e) {
        return [
            'error' => true,
            'type' => 'Some Other Error',
            'response' => [$e->getMessage()]
        ];

//        return response(
//            [
//                'error' => true,
//                'type' => 'Some Other Error',
//                'response' => [$e->getMessage()]
//            ], 404
//        );
    }
}
