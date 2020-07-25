<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateAgentSoldApplianceRequest extends FormRequest
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
            'person_id' => 'required|exists:people,id',
            'down_payment'=>'required|numeric',
            'agent_assigned_appliance_id' => 'required|exists:agent_assigned_appliances,id',
        ];
    }
}
