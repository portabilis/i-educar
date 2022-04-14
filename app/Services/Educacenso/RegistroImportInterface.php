<?php

namespace App\Services\Educacenso;

use App\Models\Educacenso\RegistroEducacenso;
use App\User;

interface RegistroImportInterface
{
    /**
     * Faz a importação dos dados a partir do model que representa o registro do educacenso
     *
     * @param RegistroEducacenso $model
     * @param int                $year
     * @param User               $user
     *
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, User $user);

    /**
     * Returna um model representando os dados do registro
     *
     * @param $arrayColumns
     *
     * @return RegistroEducacenso
     */
    public static function getModel($arrayColumns);
}
