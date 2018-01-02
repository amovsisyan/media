<?php

namespace App\Http\Requests\AdminSide\HashtagRequest;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Requests\AdminFormRequest;

class CreateHashtagRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it
        $this->request->set('hashtagNames', json_decode($this->input()['hashtagNames'], true));
        // clean alias from unnecessary symbols
        $this->request->set('hashtagAlias', Helpers::cleanToOnlyLettersNumbers($this->input()['hashtagAlias']));

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
            'hashtagAlias' => self::HASHTAG_COMMON_RULES['alias'],
            'hashtagNames.*.locale_id' => self::REQUIRE_EXISTS['locale']['id'],
            'hashtagNames.*.name' => self::HASHTAG_COMMON_RULES['hashtag']
        ];
    }
}
