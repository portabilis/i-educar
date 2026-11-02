<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\DataAwareRule;

class StudentIdentificationRule implements ImplicitRule, DataAwareRule
{
    protected $data = [];

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function passes($attribute, $value)
    {
        return !empty($value) || !empty($this->data['inep']);
    }

    public function message()
    {
        return 'É obrigatório informar o CPF ou o ID INEP.';
    }
}