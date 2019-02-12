<?php

namespace iEducar\Support\Exceptions;

use Exception as BaseException;
use Throwable;

class Exception extends BaseException implements Throwable
{
    /**
     * Return more information about error.
     *
     * @return array
     */
    public function getExtraInfo()
    {
        return [];
    }
}
