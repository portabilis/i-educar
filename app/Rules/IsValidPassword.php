<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsValidPassword implements Rule
{

    public const PASSWORD_LENGTH_DEFAULT = 8;

    public $msg = '';

    public function passes($attribute, $value)
    {
        if (strlen($value) < self::PASSWORD_LENGTH_DEFAULT) {
            $this->msg .= 'Por favor informe uma senha mais segura, com pelo menos '.self::PASSWORD_LENGTH_DEFAULT.' caracteres. ';
        }

        if (!preg_match('@[A-Z]@', $value) && !preg_match('@[a-z]@', $value)) {
            $this->msg .='O campo senha deve conter pelo menos uma letra maiúscula e uma minúscula. ';
        }

        if (!preg_match('@[0-9]@', $value)) {
            $this->msg .='O campo senha deve conter pelo menos um número. ';
        }

        if (!preg_match('@[^A-Za-z0-9]@', $value)) {
            $this->msg .='O campo senha deve conter pelo menos um símbolo. ';
        }

        return empty($this->msg);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
