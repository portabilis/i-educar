<?php

class SafeJson {

    static function encode($value, $options = 0, $depth = 512)
    {
        return self::handle(json_encode($value, $options, $depth));
    }

    static function decode($json, $assoc = false)
    {
        return self::handle(json_decode($json, $assoc));
    }

    static private function handle($value)
    {
        if ($value === false) {
            throw new Exception(json_last_error_msg());
        }

        return $value;
    }
}
