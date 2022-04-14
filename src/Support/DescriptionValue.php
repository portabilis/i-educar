<?php

namespace iEducar\Support;

trait DescriptionValue
{
    /**
     * @param int $value
     *
     * @return string
     */
    public static function getDescription($value)
    {
        return self::getDescriptiveValues()[$value] ?? null;
    }
}
