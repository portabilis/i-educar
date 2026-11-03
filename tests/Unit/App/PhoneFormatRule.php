<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


class PhoneFormatRule implements Rule
{

    private const MIN_DIGITS = 10;


    private const MAX_DIGITS = 11;


    private const MASK_CHARACTERS = '/[\s().\-]/';

    /**
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }

        $withoutMask = preg_replace(self::MASK_CHARACTERS, '', (string) $value);

        if (!ctype_digit($withoutMask)) {
            return false;
        }

        $length = strlen($withoutMask);

        return $length >= self::MIN_DIGITS && $length <= self::MAX_DIGITS;
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'O telefone deve ter DDD e 8 ou 9 dígitos, por exemplo (11) 3456-7890 ou (11) 98765-4321.';
    }
}
