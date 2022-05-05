<?php

class Portabilis_Utils_Database
{
    public static $_db;

    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    public static function db()
    {
        if (!isset(self::$_db)) {
            self::$_db = new clsBanco();
        }

        return self::$_db;
    }

    public static function fetchPreparedQuery($sql, $options = [])
    {
        $result = [];

        $defaultOptions = [
            'params' => [],
            'show_errors' => true,
            'return_only' => '',
            'messenger' => null,
            'fetchMode' => PDO::FETCH_BOTH,
        ];

        $options = self::mergeOptions($options, $defaultOptions);

        try {
            if (self::db()->setFetchMode($options['fetchMode'])->execPreparedQuery($sql, $options['params']) !== false) {
                while (self::db()->ProximoRegistro()) {
                    $result[] = self::db()->Tupla();
                }

                if (in_array($options['return_only'], ['first-line', 'first-row', 'first-record']) and count($result) > 0) {
                    $result = $result[0];
                } elseif ($options['return_only'] == 'first-field' and count($result) > 0 and count($result[0]) > 0) {
                    $result = $result[0][0];
                }
            }
        } catch (Exception $e) {
            if ($options['show_errors'] and !is_null($options['messenger'])) {
                $options['messenger']->append($e->getMessage(), 'error');
            } else {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * helper para consultas que buscam apenas o primeiro campo, considera o
     * segundo argumento o array de options esperado por fetchPreparedQuery a
     * menos que este não possua um chave params ou não seja um array, neste
     * caso o considera como params
     */
    public static function selectField($sql, $paramsOrOptions = [])
    {
        if (!is_array($paramsOrOptions) || !isset($paramsOrOptions['params'])) {
            $paramsOrOptions = ['params' => $paramsOrOptions];
        }

        $paramsOrOptions['return_only'] = 'first-field';

        return self::fetchPreparedQuery($sql, $paramsOrOptions);
    }

    /**
     * helper para consultas que buscam apenas a primeira linha considera o
     * segundo argumento o array de options esperado por fetchPreparedQuery a
     * menos que este não possua um chave params ou não seja um array, neste
     * caso o considera como params
     */
    public static function selectRow($sql, $paramsOrOptions = [])
    {
        if (!is_array($paramsOrOptions) || !isset($paramsOrOptions['params'])) {
            $paramsOrOptions = ['params' => $paramsOrOptions];
        }

        $paramsOrOptions['return_only'] = 'first-row';

        return self::fetchPreparedQuery($sql, $paramsOrOptions);
    }

    public static function arrayToPgArray(array $value): string
    {
        return '\'{' . implode(',', $value) . '}\'';
    }

    public static function pgArrayToArray($value): array
    {
        return !empty($value) ? explode(',', str_replace(['{', '}'], '', $value)) : [];
    }
}
