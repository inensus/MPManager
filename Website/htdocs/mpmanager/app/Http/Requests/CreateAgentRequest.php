<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserRequest
 * @package App\Http\Requests
 *
 *
 */
class CreateAgentRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [

            'email' => 'required|unique:agents,email',
            'name' => 'required|min:3',
            'surname' => 'required|min:3',
            'password' => 'required|min:6',
            'city_id' => 'required',
            'agent_commission_id' => 'required|exists:agent_commissions,id'
        ];
    }
}
