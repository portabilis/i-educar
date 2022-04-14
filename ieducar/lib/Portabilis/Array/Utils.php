<?php

class Portabilis_Array_Utils
{
    /**
     * Mescla $defaultArray com $array, preservando os valores de $array nos
     * casos em que ambos tem a mesma chave.
     */
    public static function merge($array, $defaultArray)
    {
        foreach ($array as $key => $value) {
            $defaultArray[$key] = $value;
        }

        return $defaultArray;
    }

    /**
     * Mescla os valores de diferentes arrays, onde no array mesclado, cada
     * valor (unico), passa a ser a chave do array.
     *
     * Ex: mergeValues(array(array(1,2), array(2,3,4)) resulta em array(1=>1, 2=>2, 3=>3, 4=>4)
     */
    public function mergeValues($arrays)
    {
        if (!is_array($arrays)) {
            $arrays = [$arrays];
        }

        $merge = [];

        foreach ($arrays as $array) {
            foreach ($array as $value) {
                if (!in_array($value, $merge)) {
                    $merge[$value] = $value;
                }
            }
        }

        return $merge;
    }

    /**
     * Insere uma chave => valor no inicio do $array, preservando os indices
     * inteiros dos arrays (sem reiniciar).
     */
    public static function insertIn($key, $value, $array)
    {
        $newArray = [$key => $value];

        foreach ($array as $key => $value) {
            $newArray[$key] = $value;
        }

        return $newArray;
    }

    public static function filterSet($arrays, $attrs = [])
    {
        if (empty($arrays)) {
            return [];
        }

        if (!is_array($arrays)) {
            $arrays = [$arrays];
        }

        $arraysFiltered = [];

        foreach ($arrays as $array) {
            $arraysFiltered[] = self::filter($array, $attrs);
        }

        return $arraysFiltered;
    }

    /**
     * Retorna um array {key => value, key => value} de atributos filtrados de
     * um outro array, podendo renomear nome dos attrs, util para filtrar um
     * array a ser retornado por uma api
     *
     * $arrays - array a ser(em) filtrado(s)
     * $attrs  - atributo ou array de atributos para filtrar objeto
     *
     * Ex: $attrs = array('cod_escola' => 'id', 'nome')
    */
    public static function filter($array, $attrs = [])
    {
        if (!is_array($attrs)) {
            $attrs = [$attrs];
        }

        $arrayFiltered = [];

        // apply filter
        foreach ($attrs as $attrName => $attrValueName) {
            if (!is_string($attrName)) {
                $attrName = $attrValueName;
            }

            $arrayFiltered[$attrValueName] = $array[$attrName];
        }

        return $arrayFiltered;
    }

    /**
     * Transforma um conjunto de arrays "chave => valor, chave => valor" em um array "id => value",
     *
     * Ex: (('id' => 1, 'nome' => 'lucas'), ('id' => 2, 'nome' => 'davila')) é
     * transformado em (1 => 'lucas', 2 => davila), caso uma mesma chave se
     * repita em mais de um array, será mantido a chave => valor do ultimo
     * array que a contem. O array retornado, será ordenado por valor.
     */
    public static function setAsIdValue($arrays, $keyAttr, $valueAtt)
    {
        if (empty($arrays)) {
            return [];
        }

        if (!is_array($arrays)) {
            $arrays = [$arrays];
        }

        $idValueArray = [];

        foreach ($arrays as $array) {
            $idValueArray = self::merge($idValueArray, self::asIdValue($array, $keyAttr, $valueAtt));
        }

        return Portabilis_Array_Utils::sortByValue($idValueArray);
    }

    /**
     * Transforma um array "chave => valor, chave => valor" em um array "id => value",
     *
     * Ex: ('id' => 1, 'nome' => 'lucas') é transformado em (1 => 'lucas')
     */
    public static function asIdValue($array, $keyAttr, $valueAtt)
    {
        return [$array[$keyAttr] => $array[$valueAtt]];
    }

    /**
     * Ordena array por uma chave usando função php usort,
     *
     * Ex: $ordenedResources = Portabilis_Array_Utils::sortByKey($resources, 'resource_att_name');
     */
    public static function sortByKey($key, $array)
    {
        usort($array, function ($a, $b) use ($key) {
            return Portabilis_Array_Utils::_keySorter($key, $a, $b);
        });

        return $array;
    }

    /**
     * Ordena array por valor mantendo as chaves associativas, usando função php uasort.
     */
    public static function sortByValue($array)
    {
        uasort($array, function ($a, $b) {
            return Portabilis_Array_Utils::_valueSorter($a, $b);
        });

        return $array;
    }

    public static function _keySorter($key, $array, $otherArray)
    {
        $a = $array[$key];
        $b = $otherArray[$key];

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    public static function _valueSorter($a, $b)
    {
        if (is_string($a)) {
            $a = Portabilis_String_Utils::unaccent($a);
        }

        if (is_string($b)) {
            $b = Portabilis_String_Utils::unaccent($b);
        }

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    /**
     * Trim values for a given array
     */
    public static function trim($array)
    {
        foreach ($array as $i => $v) {
            $array[$i] = trim($v);
        }

        return $array;
    }

    public static function arrayColumn($array, $column)
    {
        return array_map(function ($val) use ($column) {
            return $val[$column];
        }, $array);
    }
}
