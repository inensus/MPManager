<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsAndroidSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => 'required',
            'token' => 'required',
        ];
    }
}
