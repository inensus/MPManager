<?php

namespace App\Sms\BodyParsers;

use App\Models\Person\Person;

class SmsResendInformationHeader extends SmsBodyParser
{
    protected $variables = ['name', 'surname'];
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    protected function getVariableValue($variable)
    {
        if (!is_array($this->data)) {
            $person = $this->data->meter->meterParameter->owner()->first();
        } else {
            try {
                $person = Person::query()->with(['meters.meter' => function ($q) {
                    return $q->where('serial_number', $this->data['meter'])->first();
                }])->firstOrFail();
            } catch (\Exception $e) {
                return '';
            }
        }
        switch ($variable) {
            case 'name':
                $variable = $person->name;
                break;
            case 'surname':
                $variable = $person->surname;
                break;
        }
        return $variable;
    }
}
