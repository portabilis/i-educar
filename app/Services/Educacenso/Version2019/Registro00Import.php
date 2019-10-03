<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\LegacyCity;
use App\Models\LegacyComplementSchool;
use App\Models\LegacyDistrict;
use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use App\Models\LegacyNeighborhood;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacyPersonAddress;
use App\Models\LegacyPhone;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolStage;
use App\Models\LegacyStageType;
use App\Models\LegacyStreet;
use App\Models\LegacyZipCodeStreet;
use App\Models\LegacyZipCodeStreetNeighborhood;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;
use Carbon\Carbon;
use DateTime;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\OrgaoVinculadoEscola;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use Portabilis_Utils_Database;

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
     * @var int
     */
    private $year;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {

        $this->user = $user;
    }

    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param string $importString
     * @param int $year
     * @return void
     */
    public function import($importString, $year)
    {
        $this->importArray = explode(self::DELIMITER, trim($importString));
        $this->year = $year;
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
            'localizacao_diferenciada' => $this->importArray[19],
            'dependencia_administrativa' => $this->importArray[20],
            'orgao_vinculado_escola' => $this->getArrayOrgaoVinculado(),
            'mantenedora_escola_privada' => $this->getArrayMantenedora(),
            'categoria_escola_privada' => $this->importArray[31] ?: null,
            'conveniada_com_poder_publico' => $this->importArray[32] ?: null,
            'cnpj_mantenedora_principal' => $this->importArray[33] ?: null,
            'regulamentacao' => $this->importArray[35] ?: null,
            'esfera_administrativa' => $this->getEsferaAdministrativa(),
            'unidade_vinculada_outra_instituicao' => $this->importArray[39] ?: null,
            'inep_escola_sede' => $this->importArray[40] ?: null,
            'codigo_ies' => $this->importArray[41] ?: null,
        ]);

        if ($this->importArray[2] == SituacaoFuncionamento::EM_ATIVIDADE) {
            $this->createAcademicYear($school);
        }

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

    /**
     * @param LegacySchool $school
     */
    private function createAcademicYear($school)
    {
        LegacySchoolAcademicYear::create([
            'ref_cod_escola' => $school->getKey(),
            'ano' => $this->year,
            'ref_usuario_cad' => $this->user->id,
            'andamento' => 1,
            'data_cadastro' => now(),
            'ativo' => 1,
        ]);

        $stageType = LegacyStageType::first();

        if (!$stageType) {
            $stageType = LegacyStageType::create([
                'ref_usuario_cad' => $this->user->id,
                'nm_tipo' => 'Módulo Importação',
                'data_cadastro' => now(),
                'ref_cod_instituicao' => $school->institution->getKey(),
                'num_etapas' => 1,
            ]);
        }

        LegacySchoolStage::create([
            'ref_ano' => $this->year,
            'ref_ref_cod_escola' => $school->getKey(),
            'sequencial' => 1,
            'ref_cod_modulo' => $stageType->getKey(),
            'data_inicio' => DateTime::createFromFormat('d/m/Y', $this->importArray[3]),
            'data_fim' => DateTime::createFromFormat('d/m/Y', $this->importArray[4]),
            'dias_letivos' => 200,
        ]);
    }

    private function getArrayOrgaoVinculado()
    {
        $arrayOrgaoVinculado = [];

        if ($this->importArray[21]) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::EDUCACAO;
        }

        if ($this->importArray[22]) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::SEGURANCA;
        }

        if ($this->importArray[23]) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::SAUDE;
        }

        if ($this->importArray[24]) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::OUTRO;
        }

        return '{' . implode(',', $arrayOrgaoVinculado) . '}';
    }

    private function getArrayMantenedora()
    {
        $arrayMantenedora = [];

        if ($this->importArray[25]) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::GRUPOS_EMPRESARIAIS;
        }

        if ($this->importArray[26]) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::SINDICATOS_TRABALHISTAS;
        }

        if ($this->importArray[27]) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::ORGANIZACOES_NAO_GOVERNAMENTAIS;
        }

        if ($this->importArray[28]) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS;
        }

        if ($this->importArray[29]) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::SISTEMA_S;
        }

        if ($this->importArray[30]) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::OSCIP;
        }

        return '{' . implode(',', $arrayMantenedora) . '}';
    }

    private function getEsferaAdministrativa()
    {
        if ($this->importArray[36]) {
            return EsferaAdministrativa::FEDERAL;
        }

        if ($this->importArray[37]) {
            return EsferaAdministrativa::ESTADUAL;
        }

        if ($this->importArray[38]) {
            return EsferaAdministrativa::MUNICIPAL;
        }

        return null;
    }
}
