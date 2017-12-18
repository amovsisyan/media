<?php

namespace App\Http\Requests\AdminSide\PostRequest;

use App\Http\Requests\AdminFormRequest;

class AddPostPartsRequest extends AdminFormRequest
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
            'locale' => 'required', // an, ru, en ...
            'partHeader.*.*' => self::POST_PARTS_COMMON_RULES['head'],
            'partImage.*.*' => 'required|max:4000',
            'partFooter.*.*' => self::POST_PARTS_COMMON_RULES['foot']
        ];
    }
}
