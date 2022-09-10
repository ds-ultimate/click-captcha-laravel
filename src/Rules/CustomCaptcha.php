<?php

namespace Captcha\Rules;

use Captcha\Captcha;

use Illuminate\Contracts\Validation\Rule;

class CustomCaptcha implements Rule
{
    public static $ALIAS = 'customCaptcha';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(! isset($value['id'])) return false;
        if(! isset($value['key'])) return false;
        
        $model = Captcha::find((int) $value['id']);
        if($model == null) return false;
        if($model->solve_key != $value['key']) return false;
        $model->delete();
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.custom_captcha');
    }
}
