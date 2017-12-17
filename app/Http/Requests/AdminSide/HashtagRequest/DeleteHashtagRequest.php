<?php

namespace App\Http\Requests\AdminSide\HashtagRequest;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteHashtagRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => self::REQUIRE_EXISTS['hashtags']['id']
        ];
    }
}
