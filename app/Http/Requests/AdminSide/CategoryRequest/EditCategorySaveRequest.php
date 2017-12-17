<?php

namespace App\Http\Requests\AdminSide\CategoryRequest;

use App\Http\Requests\AdminFormRequest;

class EditCategorySaveRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it
        $this->request->set('localesInfo', json_decode($this->input()['localesInfo'], true));

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
            'catId' => 'required|max:10|exists:categories,id',
            'catAlias' => self::CATEGORY_COMMON_RULES['alias'],
            'localesInfo.*.locale_id' => 'required|max:10|exists:categories_locale,id',
            'localesInfo.*.name' => self::CATEGORY_COMMON_RULES['name']
        ];
    }
}
