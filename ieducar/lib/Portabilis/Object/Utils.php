<?php

class Portabilis_Object_Utils
{
    public static function filterSet($objects, $attrs = [])
    {
        if (!is_array($objects)) {
            $objects = [$objects];
        }

        $objectsFiltered = [];

        foreach ($objects as $object) {
            $objectsFiltered[] = self::filter($object, $attrs);
        }

        return $objectsFiltered;
    }

    /**
     * Retorna um array {key => value, key => value} de atributos filtrados de
     * um objeto, podendo renomear nome dos attrs, util para filtrar um objetos
     * a ser retornado por uma api.
     *
     * $objects - objeto ou array de objetos a ser(em) filtrado(s)
     * $attrs   - atributo ou array de atributos para filtrar objeto
     *
     * Ex: $attrs = array('cod_escola' => 'id', 'nome')
     */
    public static function filter($object, $attrs = [])
    {
        if (!is_array($attrs)) {
            $attrs = [$attrs];
        }

        $objectFiltered = [];

        foreach ($attrs as $keyAttr => $valueAtt) {
            if (!is_string($keyAttr)) {
                $keyAttr = $valueAtt;
            }

            $objectFiltered[$valueAtt] = $object->$keyAttr;
        }

        return $objectFiltered;
    }

    /**
     * Retorna um array { key => value, key2 => value2 }, filtrados de um array
     * (lista) de objetos, util para filtar uma lista de objetos a ser usado
     * para criar um input select.
     * $objects  - objeto ou array de objetos a ser(em) filtrado(s)
     * $keyAttr  - nome do atributo respectivo a chave, a filtrar no objeto
     * $valueAtt - nome do atributo respectivo ao valor a filtrar no objeto
     */
    public static function asIdValue($objects, $keyAttr, $valueAtt)
    {
        $objectsFiltered = [];

        if (!is_array($objects)) {
            $objects = [$objects];
        }

        foreach ($objects as $object) {
            $objectsFiltered[$object->$keyAttr] = $object->$valueAtt;
        }

        return $objectsFiltered;
    }
}
