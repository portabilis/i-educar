<?php

class Portabilis_Utils_Float
{
    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    /**
     * Limita as casas decimais de um numero float, SEM arredonda-lo, ex: para
     * 4.96, usando limit = 1, retornará 4.9 e não 5.
     */
    public static function limitDecimal($value, $options = [])
    {
        if (!is_numeric($value)) {
            throw new Exception('Value must be numeric!');
        } elseif (is_integer($value)) {
            return (float) $value;
        }

        $locale = localeconv();

        $defaultOptions = ['limit' => 2,
            'decimal_point' => $locale['decimal_point'],
            'thousands_sep' => $locale['thousands_sep']
        ];

        $options = self::mergeOptions($options, $defaultOptions);

        // split the values after and before the decimal point.
        $digits = explode($options['decimal_point'], (string) $value);

        // Caso o $value seja uma nota fechada ex: 9.0 | 8.0 ,o retorno do
        // explode() é apenas um array com uma posição ex: [ 0 => 9].
        if (! array_key_exists(1, $digits)) {
            $digits[1] = 0;
        }

        // limit the decimal using the limit option (defaults to 2), eg: .96789 will be limited to .96
        $digits[1] = substr($digits[1], 0, $options['limit']);

        // join the the digits and convert it to float, eg: '4' and '96', will be '4.96'
        return (float) ($digits[0] . '.' . $digits[1]);
    }
}
