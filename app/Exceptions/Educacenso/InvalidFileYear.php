<?php

namespace App\Exceptions\Educacenso;

use RuntimeException;

class InvalidFileYear extends RuntimeException implements ImportException
{
    public function __construct($fileYear, $selectedYear)
    {
        $message = sprintf('O ano selecionado foi %s mas o arquivo é referente ao ano %s', $selectedYear, $fileYear);

        parent::__construct($message);
    }
}
