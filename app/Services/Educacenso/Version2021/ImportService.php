<?php

namespace App\Services\Educacenso\Version2021;

use App\Services\Educacenso\Version2020\ImportService as ImportServiceVersion2020;

class ImportService extends ImportServiceVersion2020
{
    /**
     * Retorna o ano a que o service se refere
     *
     * @return int
     */
    public function getYear()
    {
        return 2021;
    }
}
