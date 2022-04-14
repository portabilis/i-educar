<?php

namespace iEducar\Modules\Educacenso;

class ArrayToCenso
{
    public static function format($array, $glue = '|')
    {
        return trim(implode($glue, $array));
    }
}
