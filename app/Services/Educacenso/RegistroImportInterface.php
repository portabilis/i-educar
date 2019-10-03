<?php

namespace App\Services\Educacenso;

use App\User;

interface RegistroImportInterface
{
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @return void
     */
    public function import();
}
