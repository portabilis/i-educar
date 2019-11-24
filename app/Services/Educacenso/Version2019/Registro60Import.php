<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\SchoolClassInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro60Import implements RegistroImportInterface
{
    /**
     * @var Registro60
     */
    private $model;

    /**
     * @var User
     */
    private $user;


    /**
     * @var int
     */
    private $year;

    /**
     * @var LegacyInstitution
     */
    private $institution;

    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int $year
     * @param $user
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->year = $year;
        $this->user = $user;
        $this->model = $model;
        $this->institution = app(LegacyInstitution::class);

        dd($this->model);

        $schoolClass = $this->getSchoolClass();
        if (!$schoolClass) {
            return;
        }


    }

    /**
     * @return LegacySchoolClass
     */
    private function getSchoolClass() : ?LegacySchoolClass
    {
        return SchoolClassInep::where('cod_turma_inep', $this->model->inepTurma)->first()->schoolClass ?? null;
    }


    /**
     * @param $arrayColumns
     * @return Registro50|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro60();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }
}
