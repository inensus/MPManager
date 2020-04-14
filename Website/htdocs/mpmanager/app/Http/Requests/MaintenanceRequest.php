<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array#
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|string',
            'name' => 'required|min:3',
            'surname' => 'required|min:3',
            'birth_date' => 'sometimes|date_format:"Y-m-d',
            'sex' => 'sometimes|in:male,female',
            'education' => 'sometimes|min:3',
            'city_id' => 'sometimes|exists:cities,id',
            'street' => 'sometimes|string|min:3',
            'email' => 'sometimes|email',
            'phone' => 'required|min:11|regex:(^\+)|numeric',
            'nationality' => 'sometimes|exists:countries,country_code',
        ];
    }
}
