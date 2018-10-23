<?php
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category  i-Educar
 * @package   Portabilis
 *
 * @since     Arquivo disponível desde a versão 1.1.0
 *
 * @version   $Id$
 */

/**
 * Portabilis_Array_Utils class.
 *
 * @author    Lucas D'Avila <lucas@lucasdavi.la>
 * @author    Rodrigo Rodrigues <rodrigogbgod@gmail.com>
 *
 * @category  i-Educar
 * @package   Portabilis
 *
 * @since     Classe disponível desde a versão 1.1.0
 *
 * @version   @@package_version@@
 */
class Portabilis_Array_Utils
{

    /**
     * Mescla $defaultArray com $array,
     * preservando os valores de $array nos casos em que ambos tem a mesma chave.
     *
     * @param array $array
     * @param array $defaultArray
     *
     * @return array
     */
    public static function merge($array, $defaultArray)
    {
        foreach ($array as $key => $value) {
            $defaultArray[$key] = $value;
        }

        return $defaultArray;
    }

    /**
     * Mescla os valores de diferentes arrays, onde no array mesclado, cada valor (unico),
     * passa a ser a chave do array.
     *
     * ex: mergeValues(array(array(1,2), array(2,3,4)) resulta em array(1=>1, 2=>2, 3=>3, 4=>4)
     *
     * @param array $arrays
     *
     * @return array
     */
    public function mergeValues($arrays)
    {
        if (! is_array($arrays)) {
            $arrays = [$arrays];
        }

        $merge = [];

        foreach ($arrays as $array) {
            foreach ($array as $value) {
                if (! in_array($value, $merge)) {
                    $merge[$value] = $value;
                }
            }
        }

        return $merge;
    }

    /**
     * Insere uma chave => valor no inicio do $array,
     * preservando os indices inteiros dos arrays (sem reiniciar)
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $array
     *
     * @return array
     */
    public static function insertIn($key, $value, $array)
    {
        $newArray = [$key => $value];

        foreach ($array as $key => $value) {
            $newArray[$key] = $value;
        }

        return $newArray;
    }

    /**
     * @param array $arrays
     * @param array $attrs
     *
     * @return array
     */
    public static function filterSet($arrays, $attrs = [])
    {
        if (empty($arrays)) {
            return [];
        }

        if (! is_array($arrays)) {
            $arrays = [$arrays];
        }

        $arraysFiltered = [];

        foreach ($arrays as $array) {
            $arraysFiltered[] = self::filter($array, $attrs);
        }

        return $arraysFiltered;
    }

    /**
     * Retorna um array {key => value, key => value}
     * de atributos filtrados de um outro array, podendo renomear nome dos attrs,
     * util para filtrar um array a ser retornado por uma api
     *
     * ex: $attrs = array('cod_escola' => 'id', 'nome')
     *
     * @param array $array array a ser(em) filtrado(s)
     * @param array $attrs atributo ou array de atributos para filtrar objeto,
     *
     * @return array
     */
    public static function filter($array, $attrs = [])
    {
        if (! is_array($attrs)) {
            $attrs = [$attrs];
        }

        $arrayFiltered = [];

        // apply filter
        foreach ($attrs as $attrName => $attrValueName) {
            if (! is_string($attrName)) {
                $attrName = $attrValueName;
            }

            $arrayFiltered[$attrValueName] = $array[$attrName];
        }

        return $arrayFiltered;
    }

    /**
     * Transforma um conjunto de arrays "chave => valor, chave => valor" em um array "id => value"
     *
     * Caso uma mesma chave se repita em mais de um array,
     * será mantido a chave => valor do ultimo array que a contem.
     * o array retornado, será ordenado por valor.
     *
     * ex: (('id' => 1, 'nome' => 'lucas'), ('id' => 2, 'nome' => 'davila'))
     * é transformado em (1 => 'lucas', 2 => davila)

     *
     * @param array  $arrays
     * @param string $keyAttr
     * @param array  $valueAtt
     */
    public static function setAsIdValue($arrays, $keyAttr, $valueAtt)
    {
        if (empty($arrays)) {
            return [];
        }

        if (! is_array($arrays)) {
            $arrays = [$arrays];
        }

        $idValueArray = [];

        foreach ($arrays as $array) {
            $idValueArray = self::merge($idValueArray, self::asIdValue($array, $keyAttr, $valueAtt));
        }

        return Portabilis_Array_Utils::sortByValue($idValueArray);
    }

    /**
     * Transforma um array "chave => valor, chave => valor" em um array "id => value"
     *
     * ex: ('id' => 1, 'nome' => 'lucas') é transformado em (1 => 'lucas')
     *
     * @param array  $array
     * @param string $keyAttr
     * @param mixed  $valueAtt
     *
     * @return array
     */
    public static function asIdValue($array, $keyAttr, $valueAtt)
    {
        return [$array[$keyAttr] => $array[$valueAtt]];
    }

    /**
     * Ordena array por uma chave usando função php usort
     *
     * ex: $ordenedResources = Portabilis_Array_Utils::sortByKey($resources, 'resource_att_name');
     *
     * @param mixed $key
     * @param array $array
     *
     * @return array
     */
    public static function sortByKey($key, $array)
    {
        usort($array, function ($a, $b) use ($key) {
            return Portabilis_Array_Utils::_keySorter($key, $a, $b);
        });

        return $array;
    }

    /**
     * Ordena array por valor mantendo as chaves associativas, usando função php uasort
     *
     * @param array $array
     *
     * @return array
     */
    public static function sortByValue($array)
    {
        uasort($array, function ($a, $b) {
            return Portabilis_Array_Utils::_valueSorter($a, $b);
        });

        return $array;
    }

    /**
     *
     * @param string $key
     * @param array  $array
     * @param array  $otherArray
     *
     * @return integer
     */
    public static function _keySorter($key, $array, $otherArray)
    {
        $a = $array[$key];
        $b = $otherArray[$key];

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    /**
     *
     * @param string $a
     * @param string $b
     *
     * @return integer
     */
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
     * Trim each values for a given array
     *
     * @param array $array
     *
     * @return array
     */
    public static function trim($array)
    {
        foreach ($array as $i => $v) {
            $array[$i] = trim($v);
        }

        return $array;
    }

    /**
     *
     * @param array  $array
     * @param string $column
     *
     * @return array
     */
    public static function arrayColumn($array, $column)
    {
        return array_map(function ($val) use ($column) {
            return $val[$column];
        }, $array);
    }
}
