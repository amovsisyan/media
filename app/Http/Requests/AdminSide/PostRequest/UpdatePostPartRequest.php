<?php

namespace App\Http\Requests\AdminSide\PostRequest;

use App\Http\Requests\AdminFormRequest;

class UpdatePostPartRequest extends AdminFormRequest
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
            'partId' => self::REQUIRE_EXISTS['post_parts']['id'],
            'head' => self::POST_PARTS_COMMON_RULES['head'],
            'body' => 'max:4000',
            'foot' => self::POST_PARTS_COMMON_RULES['foot']
        ];
    }
}
