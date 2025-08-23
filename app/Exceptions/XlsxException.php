<?php

namespace App\Exceptions;

use Exception;

class XlsxException extends Exception
{
    /**
     * Create a new exception for XLSX file issues
     */
    public function __construct(string $message = 'XLSX processing error', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}