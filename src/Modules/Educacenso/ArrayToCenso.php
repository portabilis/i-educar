<?php

namespace iEducar\Modules\Educacenso;

class ArrayToCenso
{
    public static function format($array, $glue = '|')
    {
        return implode($glue, $array);
    }

}
