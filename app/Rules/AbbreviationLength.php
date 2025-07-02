<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AbbreviationLength implements Rule
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function passes($attribute, $value)
    {
        return strlen(trim($value)) <= strlen(trim($this->name));
    }

    public function message()
    {
        return 'A abreviatura não pode ser maior que o nome da função.';
    }
}
