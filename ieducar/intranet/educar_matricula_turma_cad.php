<?php

use App\Models\LegacyRegistration;
use App\Services\SchoolClass\AvailableTimeService;

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

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_matricula_lst.php');

        $this->breadcrumb(currentPage: 'Enturmação da matrícula', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        //nova lógica
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

        $availableTimeService = new AvailableTimeService();
        $availableTimeService->onlySchoolClassesInformedOnCensus();

        $registration = LegacyRegistration::find(id: $matriculaId);

        if ($this->validarCamposObrigatoriosCenso() && !$availableTimeService->isAvailable(studentId: $registration->ref_cod_aluno, schoolClassId: $turmaDestinoId)) {
            $this->mensagem = 'O aluno já está matriculado em uma turma com esse horário.';

            return false;
        }

        $enturmacaoExists = new clsPmieducarMatriculaTurma();
        $enturmacaoExists = $enturmacaoExists->lista(
            int_ref_cod_matricula: $matriculaId,
            int_ref_cod_turma: $turmaDestinoId,
            int_ativo: 1
        );

        $enturmacaoExists = is_array(value: $enturmacaoExists) && count(value: $enturmacaoExists) > 0;

        if ($enturmacaoExists) {
            return false;
        }

        $enturmacao = new clsPmieducarMatriculaTurma(
            ref_cod_matricula: $matriculaId,
            ref_cod_turma: $turmaDestinoId,
            ref_usuario_exc: $this->pessoa_logada,
            ref_usuario_cad: $this->pessoa_logada,
            data_cadastro: null,
            data_exclusao: null,
            ativo: 1
        );

        $enturmacao->data_enturmacao = $this->data_enturmacao;

        $enturmacao->turno_id = $turnoId;
        $this->atualizaUltimaEnturmacao(matriculaId: $matriculaId);

        return $enturmacao->cadastra();
    }

    public function validaDataEnturmacao($matriculaId, $turmaDestinoId, $transferir = false)
    {
        $dataObj = new \DateTime(datetime: $this->data_enturmacao . ' 23:59:59');
        $matriculaObj = new clsPmieducarMatricula();
        $enturmacaoObj = new clsPmieducarMatriculaTurma();
        $dataAnoLetivoInicio = $matriculaObj->pegaDataAnoLetivoInicio(cod_turma: $turmaDestinoId);
        $dataAnoLetivoFim = $matriculaObj->pegaDataAnoLetivoFim(cod_turma: $turmaDestinoId);
        $exclusaoEnturmacao = $enturmacaoObj->getDataExclusaoUltimaEnturmacao(codMatricula: $matriculaId);
        $maiorDataEnturmacao = $enturmacaoObj->getMaiorDataEnturmacao(codMatricula: $matriculaId);
        $dataSaidaDaTurma = !empty($exclusaoEnturmacao)
            ? new \DateTime(datetime: $exclusaoEnturmacao)
            : null;

        $maiorDataEnturmacao = !empty($maiorDataEnturmacao)
            ? new \DateTime(datetime: $maiorDataEnturmacao)
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
            $enturmacao = new clsPmieducarMatriculaTurma;
            $enturmacao->ref_cod_matricula = $matriculaId;
            $enturmacao->ref_cod_turma = $turmaOrigemId;
            $enturmacao->sequencial = $sequencialEnturmacaoAnterior;
            $dadosEnturmacaoAnterior = $enturmacao->detalhe();
            $turnoId = $dadosEnturmacaoAnterior['turno_id'];
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
        $enturmacao = new clsPmieducarMatriculaTurma(
            ref_cod_matricula: $matriculaId,
            ref_cod_turma: $turmaId,
            ref_usuario_exc: $this->pessoa_logada,
            data_exclusao: $this->data_enturmacao,
            ativo: 0,
            sequencial: $sequencialEnturmacao
        );
        $detEnturmacao = $enturmacao->detalhe();

        $enturmacao->data_enturmacao = $detEnturmacao['data_enturmacao'];

        if ($enturmacao->edita()) {
            if ($remanejado) {
                $enturmacao->marcaAlunoRemanejado(data: $this->data_enturmacao);
            }

            return true;
        } else {
            return false;
        }
    }

    public function getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId)
    {
        $db = new clsBanco();
        $sql = 'select coalesce(max(sequencial), 1) from pmieducar.matricula_turma where ativo = 1 and ref_cod_matricula = $1 and ref_cod_turma = $2';

        if ($db->execPreparedQuery(query: $sql, params: [$matriculaId, $turmaId]) != false) {
            $db->ProximoRegistro();
            $sequencial = $db->Tupla();

            return $sequencial[0];
        }

        return 1;
    }

    public function atualizaUltimaEnturmacao($matriculaId)
    {
        $objMatriculaTurma = new clsPmieducarMatriculaTurma(ref_cod_matricula: $matriculaId);
        $ultima_turma = $objMatriculaTurma->getUltimaTurmaEnturmacao(matriculaId: $matriculaId);
        $sequencial = $objMatriculaTurma->getMaxSequencialEnturmacao(matriculaId: $matriculaId);
        $lst_ativo = $objMatriculaTurma->lista(int_ref_cod_matricula: $matriculaId, int_ref_cod_turma: $ultima_turma, int_sequencial: $sequencial);

        $ativo = $lst_ativo[0]['ativo'];
        $data_exclusao = $lst_ativo[0]['data_exclusao'];

        $dataBaseRemanejamento = $this->getDataBaseRemanejamento(
            instituicao: $objMatriculaTurma->getInstituicao()
        );

        $marcarAlunoComoRemanejado = is_null(value: $dataBaseRemanejamento) || strtotime(datetime: $dataBaseRemanejamento) < strtotime(datetime: date(format: 'Y-m-d'));

        if ($sequencial >= 1 && $marcarAlunoComoRemanejado) {
            $remanejado = true;
            $enturmacao = new clsPmieducarMatriculaTurma(
                ref_cod_matricula: $matriculaId,
                ref_cod_turma: $ultima_turma,
                ref_usuario_exc: $this->pessoa_logada,
                ref_usuario_cad: $this->pessoa_logada,
                data_exclusao: $data_exclusao,
                ativo: $ativo,
                sequencial: $sequencial,
                remanejado: $remanejado
            );

            return $enturmacao->edita();
        }

        return false;
    }

    public function Gerar()
    {
        exit;
    }

    public function Novo()
    {
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

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
