<?php

namespace App\Services\Educacenso\Version2020;

use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacySchool;
use App\Services\Educacenso\Version2019\Registro10Import as Registro10Import2019;
use App\Services\Educacenso\Version2020\Models\Registro10Model;

class Registro10Import extends Registro10Import2019
{
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int                $year
     * @param                    $user
     *
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        parent::import($model, $year, $user);

        $schoolInep = parent::getSchool();

        if (empty($schoolInep)) {
            return;
        }

        /** @var LegacySchool $school */
        $school = $schoolInep->school;
        $model = $this->model;

        $school->qtd_vice_diretor = $model->qtdViceDiretor ?: null;
        $school->qtd_orientador_comunitario = $model->qtdOrientadorComunitario ?: null;

        $school->save();
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro10|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro10Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }
}
