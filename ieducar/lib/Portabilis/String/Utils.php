<?php

class Portabilis_String_Utils
{
    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    /**
     * splits a string in a array, eg:
     * $divisors = array('-', ' '); // or $divisors = '-';
     * $options = array('limit' => 2, 'trim' => true);
     *
     * Portabilis_String_Utils::split($divisors, '123 - Some value', $options);
     * => array([0] => '123', [1] => 'Some value');
     */
    public static function split($divisors, $string, $options = [])
    {
        $result = [$string];

        $defaultOptions = ['limit' => -1, 'trim' => true];
        $options = self::mergeOptions($options, $defaultOptions);

        if (!is_array($divisors)) {
            $divisors = [$divisors];
        }

        foreach ($divisors as $divisor) {
            if (is_numeric(strpos($string, $divisor))) {
                $divisor = '/' . $divisor . '/';
                $result = preg_split($divisor, $string, $options['limit']);
                break;
            }
        }

        if ($options['trim']) {
            $result = Portabilis_Array_Utils::trim($result);
        }

        return $result;
    }

    /**
     * scapes a string, adding backslashes before characters that need to be
     * quoted, this method is useful to scape values to be inserted via
     * database queries.
     */
    public static function escape($str)
    {
        return addslashes($str);
    }

    /**
     * @deprecated
     *
     * @param string $str
     * @param array  $options
     *
     * @return string
     */
    public static function toUtf8($str, $options = [])
    {
        return $str;
    }

    public static function unaccent($str)
    {
        $fromEncoding = Portabilis_String_Utils::encoding($str);

        return iconv($fromEncoding, 'US-ASCII//TRANSLIT', $str);
    }

    public static function encoding($str)
    {
        return mb_detect_encoding($str, 'UTF-8', true);
    }

    public static function camelize($str)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

    public static function underscore($str)
    {
        $words = preg_split('/(?=[A-Z])/', $str, -1, PREG_SPLIT_NO_EMPTY);

        return strtolower(implode('_', $words));
    }

    public static function humanize($str)
    {
        $robotWords = ['_id', 'ref_cod_', 'ref_ref_cod_'];

        foreach ($robotWords as $word) {
            $str = str_replace($word, '', $str);
        }

        return str_replace('_', ' ', ucwords($str));
    }

    public static function emptyOrNull($str)
    {
        return is_null($str) || empty($str);
    }
}
