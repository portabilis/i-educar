<?php

namespace App\Services\Educacenso;

use App\Exceptions\Educacenso\NotImplementedYear;
use App\User;
use Illuminate\Http\UploadedFile;

class ImportServiceFactory
{
    /**
     * Intancia um service de importação
     *
     * @param UploadedFile $file
     * @param $year
     * @param User $user
     * @return ImportServiceInterface
     */
    static public function createImportService(UploadedFile $file, $year, User $user)
    {
        $class = self::getClassByYear($year);

        return new $class($file, $user);
    }

    /**
     * Retorna o service de importação de acordo com o ano informado
     *
     * @param $year
     * @return string
     */
    private static function getClassByYear($year)
    {
        $imports = [
            2019 => \App\Services\Educacenso\v2019\ImportService::class,
        ];

        if (isset($imports[$year])) {
            return $imports[$year];
        }

        throw new NotImplementedYear($year);
    }
}
