<?php

namespace App\Http\Requests\AdminSide\PostRequest;

use App\Http\Requests\AdminFormRequest;

class PostMainDetailsGetRequest extends AdminFormRequest
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
            'id' => self::REQUIRE_EXISTS['posts']['id'] // get param
        ];
    }

    /**
     * Add parameters to be validated
     *
     * @return array
     */
    public function all()
    {
        return array_replace_recursive(
            parent::all(),
            $this->route()->parameters()
        );
    }
}
