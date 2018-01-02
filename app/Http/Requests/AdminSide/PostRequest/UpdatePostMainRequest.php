<?php

namespace App\Http\Requests\AdminSide\PostRequest;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Requests\AdminFormRequest;

class UpdatePostMainRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it
        $this->request->set('postHashtag', json_decode($this->input()['postHashtag'], true));
        // clean alias from unnecessary symbols
        $this->request->set('postAlias', Helpers::cleanToOnlyLettersNumbers($this->input()['postAlias']));

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
            // Top Main
            'postAlias' => self::POST_COMMON_RULES['alias'],
            'postSubcategory' => self::REQUIRE_EXISTS['subcategories']['id'],
            'postHashtag.*' => self::REQUIRE_EXISTS['hashtags']['id'],

            // Main
            'header.*' => self::POST_COMMON_RULES['header'],
            'mainImage' => 'max:4000',
            'text.*' => self::POST_COMMON_RULES['text'],
        ];
    }
}
