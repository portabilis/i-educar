<?php

class SafeJson
{
    public static function encode($value, $options = 0, $depth = 512)
    {
        return self::handle(json_encode($value, $options, $depth));
    }

    public static function decode($json, $assoc = false)
    {
        return self::handle(json_decode($json, $assoc));
    }

    private static function handle($value)
    {
        if ($value === false) {
            throw new Exception(json_last_error_msg());
        }

        return $value;
    }
}
