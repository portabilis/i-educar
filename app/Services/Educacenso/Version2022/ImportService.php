<?php

namespace App\Services\Educacenso\Version2022;

use App\Services\Educacenso\Version2020\ImportService as ImportServiceVersion2020;
use App\Services\Educacenso\Version2020\Registro40Import;

class ImportService extends ImportServiceVersion2020
{
    /**
     * Retorna o ano a que o service se refere
     *
     * @return int
     */
    public function getYear()
    {
        return 2022;
    }

    /**
     * Retorna a classe responsável por importar o registro da linha
     *
     *
     * @return RegistroImportInterface
     */
    public function getRegistroById($lineId)
    {
        $arrayRegistros = [
            '00' => Registro00Import::class,
            '10' => Registro10Import::class,
            '20' => Registro20Import::class,
            '30' => Registro30Import::class,
            '40' => Registro40Import::class,
            '50' => Registro50Import::class,
            '60' => Registro60Import::class,
        ];

        if (!isset($arrayRegistros[$lineId])) {
            return;
        }

        return new $arrayRegistros[$lineId]();
    }
}
