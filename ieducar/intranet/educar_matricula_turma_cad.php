<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Services\EnrollmentService;
use App\Services\SchoolClass\AvailableTimeService;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_matricula;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_turma_origem;

    public $ref_cod_turma_destino;

    public $ref_cod_curso;

    public $data_enturmacao;

    public $sequencial;

    public function Inicializar()
    {
        $retorno = 'Novo';

        if (!$_POST) {
            $this->simpleRedirect(url: 'educar_matricula_lst.php');
        }

        foreach ($_POST as $key => $value) {
            $this->$key = $value;
        }

        $this->data_enturmacao = Portabilis_Date_Utils::brToPgSQL(date: $this->data_enturmacao);

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_matricula_lst.php');

        $this->breadcrumb(currentPage: 'Enturmação da matrícula', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        // nova lógica
        $retorno = false;
        if (is_numeric(value: $this->ref_cod_matricula)) {
            if ($this->ref_cod_turma_origem == 'remover-enturmacao-destino') {
                $retorno = $this->removerEnturmacao(matriculaId: $this->ref_cod_matricula, turmaId: $this->ref_cod_turma_destino);
            } elseif (!is_numeric(value: $this->ref_cod_turma_origem)) {
                $retorno = $this->novaEnturmacao(matriculaId: $this->ref_cod_matricula, turmaDestinoId: $this->ref_cod_turma_destino);
            } else {
                $retorno = $this->transferirEnturmacao(
                    matriculaId: $this->ref_cod_matricula,
                    turmaOrigemId: $this->ref_cod_turma_origem,
                    turmaDestinoId: $this->ref_cod_turma_destino
                );
            }
            if (!$retorno) {
                $alert = sprintf('
                <script type="text/javascript">
                    window.alert("%s");
                    window.location.href= "./educar_matricula_det.php?cod_matricula=%u";
                </script>', $this->mensagem, $this->ref_cod_matricula);
                echo $alert;
            } else {
                $this->simpleRedirect(url: 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula);
            }
        } else {
            $this->simpleRedirect(url: '/intranet/educar_aluno_lst.php');
        }
    }

    public function novaEnturmacao($matriculaId, $turmaDestinoId, $turnoId = null)
    {
        if (!$this->validaDataEnturmacao(matriculaId: $matriculaId, turmaDestinoId: $turmaDestinoId)) {
            return false;
        }

        $availableTimeService = new AvailableTimeService;
        $availableTimeService->onlySchoolClassesInformedOnCensus();

        $registration = LegacyRegistration::find(id: $matriculaId);

        if ($this->validarCamposObrigatoriosCenso() && !$availableTimeService->isAvailable(studentId: $registration->ref_cod_aluno, schoolClassId: $turmaDestinoId)) {
            $this->mensagem = 'O aluno já está matriculado em uma turma com esse horário.';

            return false;
        }

        $enturmacaoExists = LegacyEnrollment::query()
            ->active()
            ->when(is_numeric($matriculaId), fn ($q) => $q->whereRegistration($matriculaId))
            ->when(is_numeric($turmaDestinoId), fn ($q) => $q->whereSchoolClass($turmaDestinoId))
            ->whereHas('registration', function ($q) {
                $q->whereHas('student', fn ($q) => $q->active());
            })
            ->exists();

        if ($enturmacaoExists) {
            return false;
        }

        $this->atualizaUltimaEnturmacao(matriculaId: $matriculaId);

        $enturmacao = app(EnrollmentService::class)->createFromScreen(
            registrationId: $matriculaId,
            schoolClassId: $turmaDestinoId,
            createdBy: $this->pessoa_logada,
            enrollmentDate: $this->data_enturmacao,
            periodId: $turnoId
        );

        return $enturmacao !== null;
    }

    public function validaDataEnturmacao($matriculaId, $turmaDestinoId, $transferir = false)
    {
        $dataObj = new DateTime(datetime: $this->data_enturmacao . ' 23:59:59');
        $matriculaObj = new clsPmieducarMatricula;
        $dataAnoLetivoInicio = $matriculaObj->pegaDataAnoLetivoInicio(cod_turma: $turmaDestinoId);
        $dataAnoLetivoFim = $matriculaObj->pegaDataAnoLetivoFim(cod_turma: $turmaDestinoId);

        $exclusaoEnturmacao = LegacyEnrollment::query()
            ->whereRegistration($matriculaId)
            ->whereNotNull('data_exclusao')
            ->orderByDesc('data_exclusao')
            ->value('data_exclusao')?->format('Y-m-d');

        $maiorDataEnturmacao = LegacyEnrollment::query()
            ->whereRegistration($matriculaId)
            ->whereNotNull('data_enturmacao')
            ->orderByDesc('data_enturmacao')
            ->value('data_enturmacao')?->format('Y-m-d');
        $dataSaidaDaTurma = !empty($exclusaoEnturmacao)
            ? new DateTime(datetime: $exclusaoEnturmacao)
            : null;

        $maiorDataEnturmacao = !empty($maiorDataEnturmacao)
            ? new DateTime(datetime: $maiorDataEnturmacao)
            : null;

        if ($dataObj > $dataAnoLetivoFim) {
            $this->mensagem = 'Não foi possível enturmar, data de enturmação maior que data do fim do ano letivo.';

            return false;
        }

        if ($transferir && !empty($maiorDataEnturmacao) && $dataObj < $maiorDataEnturmacao) {
            $this->mensagem = 'Não foi possível enturmar, data de enturmação menor que data de entrada da última enturmação.';

            return false;
        } elseif ($dataSaidaDaTurma !== null && $dataObj < $dataSaidaDaTurma) {
            $this->mensagem = 'Não foi possível enturmar, data de enturmação menor que data de saída da última enturmação.';

            return false;
        } elseif ($dataObj < $dataAnoLetivoInicio) {
            $this->mensagem = 'Não foi possível enturmar, data de enturmação menor que data do início do ano letivo.';

            return false;
        }

        return true;
    }

    public function transferirEnturmacao($matriculaId, $turmaOrigemId, $turmaDestinoId)
    {
        if (!$this->validaDataEnturmacao(matriculaId: $matriculaId, turmaDestinoId: $turmaDestinoId, transferir: true)) {
            return false;
        }

        $turnoId = null;

        if ($this->isTurmaIntegral(turmaId: $turmaDestinoId)) {
            $sequencialEnturmacaoAnterior = $this->getSequencialEnturmacaoByTurmaId(matriculaId: $matriculaId, turmaId: $turmaOrigemId);
            $turnoId = LegacyEnrollment::query()
                ->whereRegistration($matriculaId)
                ->whereSchoolClass($turmaOrigemId)
                ->whereSequential($sequencialEnturmacaoAnterior)
                ->value('turno_id');
        }

        if ($this->removerEnturmacao(matriculaId: $matriculaId, turmaId: $turmaOrigemId, remanejado: true)) {
            return $this->novaEnturmacao(matriculaId: $matriculaId, turmaDestinoId: $turmaDestinoId, turnoId: $turnoId);
        }

        return false;
    }

    /**
     * Retorna a data base de remanejamento para a instituição.
     *
     * @param int $instituicao
     * @return string|null
     */
    public function getDataBaseRemanejamento($instituicao)
    {
        $instituicao = new clsPmieducarInstituicao(cod_instituicao: $instituicao);

        $instituicao = $instituicao->detalhe();

        return $instituicao['data_base_remanejamento'];
    }

    public function removerEnturmacao($matriculaId, $turmaId, $remanejado = false)
    {
        if (!$this->data_enturmacao) {
            $this->data_enturmacao = date(format: 'Y-m-d');
        }

        $sequencialEnturmacao = $this->getSequencialEnturmacaoByTurmaId(matriculaId: $matriculaId, turmaId: $turmaId);
        $enturmacaoService = app(EnrollmentService::class);

        DB::beginTransaction();

        $atualizou = $enturmacaoService->updateFromScreen($matriculaId, $turmaId, $sequencialEnturmacao, [
            'ref_usuario_exc' => $this->pessoa_logada,
            'data_exclusao' => $this->data_enturmacao,
            'ativo' => 0,
        ]);

        if (!$atualizou) {
            DB::rollBack();

            return false;
        }

        if ($remanejado) {
            $enturmacaoService->registerRelocation($matriculaId, $sequencialEnturmacao, $this->data_enturmacao);
        }

        DB::commit();

        return true;
    }

    public function getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId)
    {
        return LegacyEnrollment::query()
            ->active()
            ->whereRegistration($matriculaId)
            ->whereSchoolClass($turmaId)
            ->max('sequencial') ?? 1;
    }

    public function atualizaUltimaEnturmacao($matriculaId)
    {
        $sequencial = LegacyEnrollment::query()
            ->whereRegistration($matriculaId)
            ->max('sequencial');

        $ultima_turma = LegacyEnrollment::query()
            ->whereRegistration($matriculaId)
            ->when(is_numeric($sequencial), fn ($q) => $q->whereSequential($sequencial))
            ->value('ref_cod_turma');

        $ultimaEnturmacao = LegacyEnrollment::query()
            ->join('pmieducar.matricula', 'matricula.cod_matricula', 'matricula_turma.ref_cod_matricula')
            ->join('pmieducar.aluno', 'aluno.cod_aluno', 'matricula.ref_cod_aluno')
            ->where('aluno.ativo', 1)
            ->when(is_numeric($matriculaId), fn ($q) => $q->whereRegistration($matriculaId))
            ->when(is_numeric($ultima_turma), fn ($q) => $q->whereSchoolClass($ultima_turma))
            ->when(is_numeric($sequencial), fn ($q) => $q->whereSequential($sequencial))
            ->first([
                'matricula_turma.ativo',
                'matricula_turma.data_exclusao',
            ]);

        $ativo = $ultimaEnturmacao?->ativo;
        $data_exclusao = $ultimaEnturmacao?->getRawOriginal('data_exclusao');

        $instituicaoId = LegacyRegistration::query()
            ->whereKey($matriculaId)
            ->join('pmieducar.escola', 'escola.cod_escola', 'matricula.ref_ref_cod_escola')
            ->value('escola.ref_cod_instituicao');

        $dataBaseRemanejamento = $this->getDataBaseRemanejamento(instituicao: $instituicaoId);

        $marcarAlunoComoRemanejado = is_null(value: $dataBaseRemanejamento) || strtotime(datetime: $dataBaseRemanejamento) < strtotime(datetime: date(format: 'Y-m-d'));

        if ($sequencial >= 1 && $marcarAlunoComoRemanejado) {
            $remanejado = true;

            return app(EnrollmentService::class)->updateFromScreen($matriculaId, $ultima_turma, $sequencial, [
                'ref_usuario_exc' => $this->pessoa_logada,
                'ref_usuario_cad' => $this->pessoa_logada,
                'data_exclusao' => $data_exclusao,
                'ativo' => $ativo,
                'remanejado' => $remanejado,
            ]);
        }

        return false;
    }

    public function Gerar()
    {
        exit;
    }

    public function Novo() {}

    public function Editar() {}

    public function Excluir() {}

    public function isTurmaIntegral($turmaId)
    {
        $turma = new clsPmieducarTurma(cod_turma: $turmaId);
        $turma = $turma->detalhe();

        return $turma['turma_turno_id'] == clsPmieducarTurma::TURNO_INTEGRAL;
    }

    public function Formular()
    {
        $this->title = 'Matricula Turma';
        $this->processoAp = 578;
    }
};
