<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
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
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolStage;
use App\Models\LegacyStageType;
use App\Models\LegacyPlace;
use App\Models\LegacyPostalCodeStreet;
use App\Models\LegacyPostalCodeStreetNeighborhood;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;
use DateTime;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\OrgaoVinculadoEscola;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;

class Registro00Import implements RegistroImportInterface
{
    /**
     * @var Registro00
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
     * @var \Illuminate\Contracts\Foundation\Application
     */

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
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;
        $this->institution = app(LegacyInstitution::class);
        $this->getOrCreateSchool();
    }

    /**
     * Retorna uma escola existente ou cria uma nova
     *
     * @return LegacySchool
     */
    private function getOrCreateSchool()
    {
        $schoolInep = SchoolInep::where('cod_escola_inep', $this->model->codigoInep)->first();

        if ($schoolInep) {
            return $schoolInep->school;
        }

        $person = LegacyPerson::create([
            'nome' => $this->model->nome,
            'tipo' => 'J',
            'email' => $this->model->email,
            'data_cad' => now(),
            'situacao' => 'P',
            'origem_gravacao' => 'U',
            'operacao' => 'I',
        ]);

        $organization = LegacyOrganization::create([
            'idpes' => $person->idpes,
            'cnpj' => $this->model->cnpjEscolaPrivada ?: rand(1, 99) . rand(1, 999) . rand(1, 999) . rand(1, 9999) . rand(1, 99),
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
            'fantasia' => $this->model->nome,
        ]);

        $educationNetword = self::getOrCreateEducationNetwork($this->user);

        $school = LegacySchool::create([
            'situacao_funcionamento' => $this->model->situacaoFuncionamento,
            'sigla' => mb_substr($this->model->nome, 0, 5, 'UTF-8'),
            'data_cadastro' => now(),
            'ativo' => 1,
            'ref_idpes' => $organization->getKey(),
            'ref_usuario_cad' => $this->user->id,
            'ref_cod_escola_rede_ensino' => $educationNetword->getKey(),
            'ref_cod_instituicao' => $this->institution->id,
            'zona_localizacao' => $this->model->zonaLocalizacao,
            'localizacao_diferenciada' => $this->model->localizacaoDiferenciada,
            'dependencia_administrativa' => $this->model->dependenciaAdministrativa,
            'orgao_vinculado_escola' => $this->getArrayOrgaoVinculado(),
            'mantenedora_escola_privada' => $this->getArrayMantenedora(),
            'categoria_escola_privada' => $this->model->categoriaEscolaPrivada ?: null,
            'conveniada_com_poder_publico' => $this->model->conveniadaPoderPublico ?: null,
            'cnpj_mantenedora_principal' => $this->model->cnpjMantenedoraPrincipal ?: null,
            'regulamentacao' => $this->model->regulamentacao ?: null,
            'esfera_administrativa' => $this->getEsferaAdministrativa(),
            'unidade_vinculada_outra_instituicao' => $this->model->unidadeVinculada ?: null,
            'inep_escola_sede' => $this->model->inepEscolaSede ?: null,
            'codigo_ies' => $this->model->codigoIes ?: null,
        ]);

        if ($this->model->situacaoFuncionamento == SituacaoFuncionamento::EM_ATIVIDADE) {
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
            'ref_cod_instituicao' => $this->institution->id,
            'data_cadastro' => now(),
        ]);
    }

    private function createAddress($school)
    {
        $personAddress = LegacyPersonAddress::where('idpes', $school->ref_idpes)->exists();
        if ($personAddress) {
            return;
        }

        $city = LegacyCity::where('cod_ibge', $this->model->codigoIbgeMunicipio)->first();
        if (!$city) {
            return;
        }

        $district = $this->getDistrict($city, $this->model->codigoIbgeDistrito);
        if (!$district) {
            return;
        }

        $neighborhood = LegacyNeighborhood::firstOrCreate([
            'idmun' => $city->getKey(),
            'nome' => $this->model->bairro,
            'iddis' => $district->getKey(),
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
            'zona_localizacao' => $this->model->zonaLocalizacao,
        ]);

        $street = LegacyPlace::firstOrCreate([
            'idtlog' => 'RUA',
            'nome' => trim(str_replace('RUA', '', $this->model->logradouro)),
            'idmun' => $city->getKey(),
            'ident_oficial' => 'N',
            'origem_gravacao' => 'M',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
        ]);

        LegacyPostalCodeStreet::firstOrCreate([
            'cep' => $this->model->cep,
            'idlog' => $street->getKey(),
            'origem_gravacao' => 'U',
            'idpes_cad' => $this->user->id,
            'data_cad' => now(),
            'operacao' => 'I',
        ]);

        LegacyPostalCodeStreetNeighborhood::firstOrCreate([
            'cep' => $this->model->cep,
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
            'cep' => $this->model->cep,
            'idlog' => $street->getKey(),
            'numero' => (int) (is_numeric($this->model->numero) ? $this->model->numero : null),
            'complemento' => $this->model->complemento,
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
            'cod_escola_inep' => $this->model->codigoInep,
            'nome_inep' => '-',
            'fonte' => 'importador',
            'created_at' => now(),
        ]);
    }

    private function createPhones($school)
    {
        if ($this->model->telefone) {
            LegacyPhone::create([
                'idpes' => $school->ref_idpes,
                'tipo' => 1,
                'ddd' => $this->model->ddd,
                'fone' => $this->model->telefone,
                'idpes_cad' => $this->user->id,
                'origem_gravacao' => 'M',
                'operacao' => 'I',
                'data_cad' => now(),
            ]);
        }


        if ($this->model->telefoneOutro) {
            LegacyPhone::create([
                'idpes' => $school->ref_idpes,
                'tipo' => 3,
                'ddd' => $this->model->ddd,
                'fone' => $this->model->telefoneOutro,
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
            'data_inicio' => DateTime::createFromFormat('d/m/Y', $this->model->inicioAnoLetivo),
            'data_fim' => DateTime::createFromFormat('d/m/Y', $this->model->fimAnoLetivo),
            'dias_letivos' => 200,
        ]);
    }

    private function getArrayOrgaoVinculado()
    {
        $arrayOrgaoVinculado = [];

        if ($this->model->orgaoEducacao) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::EDUCACAO;
        }

        if ($this->model->orgaoSeguranca) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::SEGURANCA;
        }

        if ($this->model->orgaoSaude) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::SAUDE;
        }

        if ($this->model->orgaoOutro) {
            $arrayOrgaoVinculado[] = OrgaoVinculadoEscola::OUTRO;
        }

        return '{' . implode(',', $arrayOrgaoVinculado) . '}';
    }

    private function getArrayMantenedora()
    {
        $arrayMantenedora = [];

        if ($this->model->mantenedoraEmpresa) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::GRUPOS_EMPRESARIAIS;
        }

        if ($this->model->mantenedoraSindicato) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::SINDICATOS_TRABALHISTAS;
        }

        if ($this->model->mantenedoraOng) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::ORGANIZACOES_NAO_GOVERNAMENTAIS;
        }

        if ($this->model->mantenedoraInstituicoes) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS;
        }

        if ($this->model->mantenedoraSistemaS) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::SISTEMA_S;
        }

        if ($this->model->mantenedoraOscip) {
            $arrayMantenedora[] = MantenedoraDaEscolaPrivada::OSCIP;
        }

        return '{' . implode(',', $arrayMantenedora) . '}';
    }

    private function getEsferaAdministrativa()
    {
        if ($this->model->esferaFederal) {
            return EsferaAdministrativa::FEDERAL;
        }

        if ($this->model->esferaEstadual) {
            return EsferaAdministrativa::ESTADUAL;
        }

        if ($this->model->esferaMunicipal) {
            return EsferaAdministrativa::MUNICIPAL;
        }

        return null;
    }

    public static function getModel($arrayColumns)
    {
        $registro = new Registro00();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }

    /**
     * @param LegacyCity $city
     * @param string $codigoIbgeDistrito
     * @return LegacyDistrict|null
     */
    private function getDistrict(LegacyCity $city, string $codigoIbgeDistrito)
    {
        if (!$codigoIbgeDistrito) {
            return null;
        }

        $codigoIbgeDistrito = substr($codigoIbgeDistrito, -2, 2);

        return LegacyDistrict::where('idmun', $city->getKey())->where('cod_ibge', $codigoIbgeDistrito)->first();
    }
}
