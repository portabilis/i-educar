<?php

namespace App\Services\Educacenso;

use App\User;

interface RegistroImportInterface
{
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param string $importString
     * @param int $year
     * @return void
     */
    public function import($importString, $year);
}
