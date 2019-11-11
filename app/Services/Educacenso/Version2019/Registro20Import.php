<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro20;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacySchool;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro20Import implements RegistroImportInterface
{
    /**
     * @var Registro20
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
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int $year
     * @param $user
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;

        $schoolInep = $this->getSchool();

        if (empty($schoolInep)) {
            return;
        }

        /** @var LegacySchool $school */
        $school = $schoolInep->school;
        $model = $this->model;

        
    }

    private function getSchool()
    {
        return SchoolInep::where('cod_escola_inep', $this->model->codigoEscolaInep)->first();
    }

    /**
     * @param $arrayColumns
     * @return Registro10|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro20();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }

    public static function getComponentes()
    {
        return [
            1 => 'Química',
            2 => 'Física',
            3 => 'Matemática',
            4 => 'Biologia',
            5 => 'Ciências',
            6 => 'Língua/Literatura portuguesa',
            7 => 'Língua/Literatura extrangeira - Inglês',
            8 => 'Língua/Literatura extrangeira - Espanhol',
            30 => 'Língua/Literatura extrangeira - Francês',
            9 => 'Língua/Literatura extrangeira - Outra',
            10 => 'Artes (educação artística, teatro, dança, música, artes plásticas e outras)',
            11 => 'Educação física',
            12 => 'História',
            13 => 'Geografia',
            14 => 'Filosofia',
            28 => 'Estudos sociais',
            29 => 'Sociologia',
            16 => 'Informática/Computação',
            17 => 'Disciplinas dos Cursos Técnicos Profissionais;',
            23 => 'LIBRAS',
            25 => 'Disciplinas pedagógicas',
            26 => 'Ensino religioso',
            27 => 'Língua indígena',
            31 => 'Língua Portuguesa como Segunda Língua',
            32 => 'Estágio Curricular Supervisionado',
            99 => 'Outras disciplinas'
        ];
    }
}
