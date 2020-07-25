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
            'person_id' => 'required|exists:people,id|unique:agents',
            'email' => 'required|unique:agents,email',
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'mini_grid_id' => 'required',
            'agent_commission_id' => 'required|exists:agent_commissions,id'
        ];
    }
}
