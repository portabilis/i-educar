<?php

namespace App\Exceptions\SchoolClass;

use RuntimeException;

class HasDataInDiario extends RuntimeException implements DisciplinesValidationException
{
    public function __construct($messages)
    {
        $message = implode('<br>', $messages);

        parent::__construct($message);
    }
}
