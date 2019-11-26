<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacyEnrollment;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentTransport;
use App\Models\SchoolClassInep;
use App\Models\Student;
use App\Models\StudentInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;
use App_Model_MatriculaSituacao;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoAluno;
use iEducar\Modules\Educacenso\Model\VeiculoTransporteEscolar;

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

        $schoolClass = $this->getSchoolClass();
        if (!$schoolClass) {
            return;
        }

        $student = $this->getStudent();
        if (!$student) {
            return;
        }

        $this->storeStudentData($student);

        $registration = $this->getOrCreateRegistration($schoolClass, $student);
        $this->getOrCreateEnrollment($schoolClass, $registration);
    }

    /**
     * @return LegacySchoolClass
     */
    private function getSchoolClass(): ?LegacySchoolClass
    {
        return SchoolClassInep::where('cod_turma_inep', $this->model->inepTurma)->first()->schoolClass ?? null;
    }

    /**
     * @return LegacyStudent|null
     */
    private function getStudent()
    {
        return StudentInep::where('cod_aluno_inep', $this->model->inepAluno)->first()->student ?? null;
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

    /**
     * @param LegacySchoolClass $schoolClass
     * @param LegacyStudent $student
     * @return LegacyRegistration
     */
    private function getOrCreateRegistration(LegacySchoolClass $schoolClass, LegacyStudent $student)
    {
        return LegacyRegistration::firstOrCreate(
            [
                'ref_ref_cod_serie' => $schoolClass->grade->getKey(),
                'ref_cod_aluno' => $student->getKey(),
                'ano' => $this->year,
            ],
            [
                'ref_usuario_cad' => $this->user->getKey(),
                'ativo' => 1,
                'aprovado' => App_Model_MatriculaSituacao::APROVADO,
                'ref_cod_curso' => $schoolClass->course->getKey(),
                'ref_ref_cod_escola' => $schoolClass->school->getKey(),
                'data_matricula' => now(),
                'data_cadastro' => now(),
                'ultima_matricula' => 1,
            ]
        );
    }

    /**
     * @param LegacySchoolClass $schoolClass
     * @param LegacyRegistration $registration
     * @return LegacyEnrollment
     */
    private function getOrCreateEnrollment(LegacySchoolClass $schoolClass, LegacyRegistration $registration)
    {
        $maxSequencial = LegacyEnrollment::where('ref_cod_matricula', $registration->getKey())->max('sequencial') ?: 1;

        return LegacyEnrollment::firstOrCreate(
            [
                'ref_cod_matricula' => $registration->getKey(),
                'ref_cod_turma' => $schoolClass->getKey(),
            ],
            [
                'data_cadastro' => now(),
                'data_enturmacao' => now(),
                'ativo' => 1,
                'etapa_educacenso' => $this->model->etapaAluno,
                'tipo_atendimento' => $this->getArrayTipoAtendimento(),
                'sequencial' => $maxSequencial,
                'ref_usuario_cad' => $this->user->getKey(),
            ]
        );
    }

    /**
     * @return string
     */
    private function getArrayTipoAtendimento()
    {
        $arrayTipoAtendimento = [];

        if ($this->model->tipoAtendimentoDesenvolvimentoFuncoesGognitivas) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::DESENVOLVIMENTO_FUNCOES_COGNITIVAS;
        }

        if ($this->model->tipoAtendimentoEnriquecimentoCurricular) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENRIQUECIMENTO_CURRICULAR;
        }

        if ($this->model->tipoAtendimentoDesenvolvimentoVidaAutonoma) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::DESENVOLVIMENTO_VIDA_AUTONOMA;
        }

        if ($this->model->tipoAtendimentoEnriquecimentoCurricular) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENRIQUECIMENTO_CURRICULAR;
        }

        if ($this->model->tipoAtendimentoEnsinoInformaticaAcessivel) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_INFORMATICA_ACESSIVEL;
        }

        if ($this->model->tipoAtendimentoEnsinoLibras) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_LIBRAS;
        }

        if ($this->model->tipoAtendimentoEnsinoLinguaPortuguesa) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_LINGUA_PORTUGUESA;
        }

        if ($this->model->tipoAtendimentoEnsinoSoroban) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_SOROBAN;
        }

        if ($this->model->tipoAtendimentoEnsinoBraile) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_BRAILE;
        }

        if ($this->model->tipoAtendimentoEnsinoOrientacaoMobilidade) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_ORIENTACAO_MOBILIDADE;
        }

        if ($this->model->tipoAtendimentoEnsinoCaa) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_CAA;
        }

        if ($this->model->tipoAtendimentoEnsinoRecursosOpticosNaoOpticos) {
            $arrayTipoAtendimento[] = TipoAtendimentoAluno::ENSINO_RECURSOS_OPTICOS_E_NAO_OPTICOS;
        }

        return $this->getPostgresIntegerArray($arrayTipoAtendimento);
    }

    /**
     * @param $array
     * @return string
     */
    private function getPostgresIntegerArray($array)
    {
        return '{' . implode(',', $array) . '}';
    }

    /**
     * @param LegacyStudent $student
     */
    private function storeStudentData(LegacyStudent $student)
    {
        LegacyStudentTransport::updateOrCreate(
            ['aluno_id' => $student->getKey()],
            [
                'responsavel' => (int)$this->model->poderPublicoResponsavelTransporte,
                'user_id' => $this->user->getKey(),
            ]
        );

        $student->recebe_escolarizacao_em_outro_espaco = $this->model->recebeEscolarizacaoOutroEspacao;
        $student->veiculo_transporte_escolar = $this->getArrayVeiculoTransporte();

        $student->save();


    }

    private function getArrayVeiculoTransporte()
    {
        $arrayVeiculoTransporte = [];

        if ($this->model->veiculoTransporteVanKonbi) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::VAN_KOMBI;
        }

        if ($this->model->veiculoTransporteMicroonibus) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::MICROONIBUS;
        }

        if ($this->model->veiculoTransporteOnibus) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::ONIBUS;
        }

        if ($this->model->veiculoTransporteBicicleta) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::BICICLETA;
        }

        if ($this->model->veiculoTransporteTracaoAnimal) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::TRACAO_ANIMAL;
        }

        if ($this->model->veiculoTransporteOutro) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::OUTRO;
        }

        if ($this->model->veiculoTransporteAquaviarioCapacidade5) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::CAPACIDADE_5;
        }

        if ($this->model->veiculoTransporteAquaviarioCapacidade5a15) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::CAPACIDADE_5_15;
        }

        if ($this->model->veiculoTransporteAquaviarioCapacidade15a35) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::CAPACIDADE_15_35;
        }

        if ($this->model->veiculoTransporteAquaviarioCapacidadeAcima35) {
            $arrayVeiculoTransporte[] = VeiculoTransporteEscolar::CAPACIDADE_35;
        }

        return $this->getPostgresIntegerArray($arrayVeiculoTransporte);
    }
}
