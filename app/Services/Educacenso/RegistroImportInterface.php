<?php

namespace App\Services\Educacenso;

interface RegistroImportInterface
{
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param array $importString
     * @return void
     */
    public static function import($importString);
}
