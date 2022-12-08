<?php

namespace App\Services\Educacenso\Version2022;

use App\Models\Educacenso\Registro20;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacySchoolClass;
use App\Services\Educacenso\Version2019\Registro20Import as Registro20Import2019;
use App\Services\Educacenso\Version2022\Models\Registro20Model;

class Registro20Import extends Registro20Import2019
{
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;

        parent::import($model, $year, $user);

        $model = $this->model;

        $schoolClassInep = parent::getSchoolClass();

        $schoolClass = LegacySchoolClass::find($schoolClassInep->cod_turma);

        $schoolClass->estrutura_curricular = transformDBArrayInString($model->estruturaCurricular) ?: null;
        $schoolClass->formas_organizacao_turma = $model->formasOrganizacaoTurma ?: null;
        $schoolClass->unidade_curricular = transformDBArrayInString($model->unidadesCurriculares) ?: null;

        $schoolClass->save();
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro20|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro20Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }

    public static function getComponentes()
    {
        $componentes = parent::getComponentes();
        $componentes[33] = 'Projeto de vida';

        return $componentes;
    }
}
