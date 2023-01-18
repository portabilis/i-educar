<?php

namespace App\Services\Reports;

class Util
{
    public static function format(string|int|null $value, $decimalPlaces = 1): string
    {
        return str_replace('.', ',', bcdiv($value, 1, $decimalPlaces));
    }
}
