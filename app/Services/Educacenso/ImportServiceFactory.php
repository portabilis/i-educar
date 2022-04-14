<?php

namespace App\Services\Educacenso;

use App\Exceptions\Educacenso\NotImplementedYear;
use App\Services\Educacenso\Version2019\ImportService as ImportService2019;
use App\Services\Educacenso\Version2020\ImportService as ImportService2020;
use App\Services\Educacenso\Version2021\ImportService as ImportService2021;
use DateTime;

class ImportServiceFactory
{
    /**
     * Intancia um service de importação
     *
     * @param          $year
     * @param DateTime $registrationDate
     *
     * @return ImportService
     */
    public static function createImportService($year, $registrationDate)
    {
        /** @var ImportService $class */
        $class = self::getClassByYear($year);

        $class = new $class();
        $class->registrationDate = $registrationDate;

        return $class;
    }

    /**
     * Retorna o service de importação de acordo com o ano informado
     *
     * @param $year
     *
     * @return string
     */
    private static function getClassByYear($year)
    {
        $imports = [
            2019 => ImportService2019::class,
            2020 => ImportService2020::class,
            2021 => ImportService2021::class,
        ];

        if (isset($imports[$year])) {
            return $imports[$year];
        }

        throw new NotImplementedYear($year);
    }
}
