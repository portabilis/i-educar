<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\LegacyCity;
use App\Models\LegacyDistrict;
use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use App\Models\LegacyNeighborhood;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacyPersonAddress;
use App\Models\LegacyPhone;
use App\Models\LegacySchool;
use App\Models\LegacyStreet;
use App\Models\LegacyZipCodeStreet;
use App\Models\LegacyZipCodeStreetNeighborhood;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro00Import implements RegistroImportInterface
{
    const DELIMITER = '|';

    /**
     * @var string
     */
    private $importArray;
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
        $this->importArray = explode(self::DELIMITER, $importString);
        $this->user = $user;
    }
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param string $importArray
     * @param User $user
     * @return void
     */
    public function import()
    {
        $school = $this->getOrCreateSchool();
    }

    /**
     * Retorna uma escola existente ou cria uma nova
     *
     * @return LegacySchool
     */
    private function getOrCreateSchool()
    {
        $schoolInep = SchoolInep::where('cod_escola_inep', $this->importArray[1])->first();

        if ($schoolInep) {
            return $schoolInep->school;
        }

        $person = LegacyPerson::create([
            'nome' => $this->importArray[5],
            'tipo' => 'J',
            'email' => $this->importArray[16],
            'data_cad' => now(),
            'situacao' => 'P',
            'origem_gravacao' => 'U',
            'operacao' => 'I',
        ]);

        $organization = LegacyOrganization::create([
            'idpes' => $person->idpes,
            'cnpj' => $this->importArray[34] ?: '00000000000100',
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
            'fantasia' => $this->importArray[5],
        ]);

        $educationNetword = self::getOrCreateEducationNetwork($this->user);

        $school = LegacySchool::create([
            'situacao_funcionamento' => $this->importArray[2],
            'sigla' => mb_substr($this->importArray[5], 0, 5, 'UTF-8'),
            'data_cadastro' => now(),
            'ativo' => 1,
            'ref_idpes' => $organization->getKey(),
            'ref_usuario_cad' => $this->user->id,
            'ref_cod_escola_rede_ensino' => $educationNetword->getKey(),
            'ref_cod_instituicao' => LegacyInstitution::active()->first()->id,
            'zona_localizacao' => $this->importArray[18],
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
        $personAddress = LegacyPersonAddress::where('idpes', $school->ref_idpes)->exists();
        if ($personAddress) {
            return;
        }

        $city = LegacyCity::where('cod_ibge', $this->importArray[7])->first();
        if (!$city) {
            return;
        }

        $district = LegacyDistrict::where('idmun', $city->getKey())->where('cod_ibge', $this->importArray[8])->first();
        if (!$district) {
            return;
        }

        $neighborhood = LegacyNeighborhood::firstOrCreate([
            'idmun' => $city->getKey(),
            'nome' => $this->importArray[12],
            'iddis' => $district->getKey(),
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
            'zona_localizacao' => $this->importArray[18],
        ]);

        $street = LegacyStreet::firstOrCreate([
            'idtlog' => 'RUA',
            'nome' => trim(str_replace('RUA', '', $this->importArray[9])),
            'idmun' => $city->getKey(),
            'ident_oficial' => 'N',
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
        ]);

        LegacyZipCodeStreet::firstOrCreate([
            'cep' => $this->importArray[6],
            'idlog' => $street->getKey(),
            'origem_gravacao' => 'U',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
        ]);

        LegacyZipCodeStreetNeighborhood::firstOrCreate([
            'cep' => $this->importArray[6],
            'idlog' => $street->getKey(),
            'idbai' => $neighborhood->getKey(),
            'origem_gravacao' => 'U',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
        ]);

        LegacyPersonAddress::create([
            'idpes' => $school->ref_idpes,
            'tipo' => '1',
            'cep' => $this->importArray[6],
            'idlog' => $street->getKey(),
            'numero' => $this->importArray[10],
            'complemento' => $this->importArray[11],
            'idbai' => $neighborhood->getKey(),
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
        ]);
    }

    private function createSchoolInep($school)
    {
        SchoolInep::create([
            'cod_escola' => $school->getKey(),
            'cod_escola_inep' => $this->importArray[1],
            'nome_inep' => '-',
            'fonte' => 'importador',
            'created_at' => now(),
        ]);
    }

    private function createPhones($school)
    {
        if ($this->importArray[14]) {
            LegacyPhone::create([
                'idpes' => $school->ref_idpes,
                'tipo' => 1,
                'ddd' => $this->importArray[13],
                'fone' => $this->importArray[14],
                'idpes_cad' => $this->user->id,
                'origem_gravacao' => 'M',
                'operacao' => 'I',
                'data_cad' => now(),
            ]);
        }

        if ($this->importArray[15]) {
            LegacyPhone::create([
                'idpes' => $school->ref_idpes,
                'tipo' => 3,
                'ddd' => $this->importArray[13],
                'fone' => $this->importArray[15],
                'idpes_cad' => $this->user->id,
                'origem_gravacao' => 'M',
                'operacao' => 'I',
                'data_cad' => now(),
            ]);
        }
    }
}
