<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacyPersonAddress;
use App\Models\LegacyPhone;
use App\Models\LegacySchool;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro00Import implements RegistroImportInterface
{
    const DELIMITER = '|';

    /**
     * @var string
     */
    private $importString;
    /**
     * @var User
     */
    private $user;

    /**
     * @param $importString
     * @param User $user
     */
    public function __construct($importString, User $user)
    {
        $this->importString = $importString;
        $this->user = $user;
    }
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param string $importString
     * @param User $user
     * @return void
     */
    public function import()
    {
        $importArray = explode(self::DELIMITER, $this->importString);
        $school = $this->getOrCreateSchool($importArray);
    }

    /**
     * Retorna uma escola existente ou cria uma nova
     *
     * @param array $importString
     * @return LegacySchool
     */
    private function getOrCreateSchool(array $importString)
    {
        $schoolInep = SchoolInep::where('cod_escola_inep', $importString[1])->first();

        if ($schoolInep) {
            return $schoolInep->school;
        }

        $person = LegacyPerson::create([
            'nome' => $importString[5],
            'tipo' => 'J',
            'email' => $importString[16],
            'data_cad' => now(),
            'situacao' => 'P',
            'origem_gravacao' => 'U',
            'operacao' => 'I',
        ]);

        $organization = LegacyOrganization::create([
            'idpes' => $person->idpes,
            'cnpj' => $importString[34] ?: '00000000000100',
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
            'fantasia' => $importString[5],
        ]);

        $educationNetword = self::getOrCreateEducationNetwork($this->user);

        $school = LegacySchool::create([
            'situacao_funcionamento' => $importString[2],
            'sigla' => mb_substr($importString[5], 0, 5, 'UTF-8'),
            'data_cadastro' => now(),
            'ativo' => 1,
            'ref_idpes' => $organization->getKey(),
            'ref_usuario_cad' => $this->user->id,
            'ref_cod_escola_rede_ensino' => $educationNetword->getKey(),
            'ref_cod_instituicao' => LegacyInstitution::active()->first()->id,
            'zona_localizacao' => $importString[18],
        ]);

        $this->createAddress($school);
        $this->createSchoolInep($school);
        $this->createPhones($school);
    }

    private function getOrCreateEducationNetwork()
    {
        $educationNetwork = LegacyEducationNetwork::all()->first();

        if ($educationNetwork) {
            return $educationNetwork;
        }

        return LegacyEducationNetwork::create([
            'ref_usuario_cad' => $this->user->id,
            'nm_rede' => 'Importação Educacenso',
            'ativo' => 1,
            'ref_cod_instituicao' => LegacyInstitution::active()->first()->id,
            'data_cadastro' => now(),
        ]);
    }

    private function createAddress($school)
    {
        $personAddress = LegacyPersonAddress::find($school->ref_idpes)->exists();
        if ($personAddress) {
            return;
        }


    }

    private function createSchoolInep($school)
    {
        SchoolInep::create([
            'cod_escola' => $school->getKey(),
            'cod_escola_inep' => $this->importString[15],
            'nome_inep' => '-',
            'fonte' => 'importador',
            'created_at' => now(),
        ]);
    }

    private function createPhones($school)
    {
        if ($this->importString[14]) {
            LegacyPhone::create([
                'idpes' => $school->ref_idpes,
                'tipo' => 1,
                'ddd' => $this->importString[13],
                'fone' => $this->importString[14],
                'idpes_cad' => $this->user->id,
                'origem_gravacao' => 'M',
                'operacao' => 'I',
                'data_cad' => now(),
            ]);
        }

        if ($this->importString[15]) {
            LegacyPhone::create([
                'idpes' => $school->ref_idpes,
                'tipo' => 3,
                'ddd' => $this->importString[13],
                'fone' => $this->importString[15],
                'idpes_cad' => $this->user->id,
                'origem_gravacao' => 'M',
                'operacao' => 'I',
                'data_cad' => now(),
            ]);
        }
    }
}
