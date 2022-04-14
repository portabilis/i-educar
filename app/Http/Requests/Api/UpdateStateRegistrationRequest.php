<?php

namespace App\Http\Requests\Api;

use App\Rules\StateRegistrationFormatRule;
use App\Rules\StateRegistrationUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStateRegistrationRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'state_registration_id' => [
                new StateRegistrationFormatRule(),
                new StateRegistrationUniqueRule($this->student),
            ],
        ];
    }

    /**
     * @return string
     */
    public function getStateRegistration()
    {
        return $this->input('state_registration_id');
    }
}
