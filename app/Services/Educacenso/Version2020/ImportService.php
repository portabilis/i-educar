<?php

namespace App\Services\Educacenso\Version2020;

use App\Services\Educacenso\ImportService as GeneralImportService;
use App\Services\Educacenso\RegistroImportInterface;
use App\Services\Educacenso\Version2019\Registro00Import;
use App\Services\Educacenso\Version2019\Registro20Import;
use App\Services\Educacenso\Version2019\Registro50Import;
use App\Services\Educacenso\Version2019\Registro60Import;

class ImportService extends GeneralImportService
{
    /**
     * Retorna o ano a que o service se refere
     *
     * @return int
     */
    public function getYear()
    {
        return 2020;
    }

    /**
     * Retorna o nome da escola a partir da string do arquivo de importação
     *
     * @param $school
     *
     * @return string
     */
    public function getSchoolNameByFile($school)
    {
        $columns = explode(self::DELIMITER, $school[0]);

        return $columns[5];
    }

    /**
     * Retorna a classe responsável por importar o registro da linha
     *
     * @param $lineId
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

    public function adaptData()
    {
        return;
    }
}
