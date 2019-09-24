<?php

use App\Models\LegacyRegistration;
use App\Services\SchoolClass\AvailableTimeService;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
        $this->processoAp = 578;
    }
}

class indice extends clsCadastro
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
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        foreach ($_POST as $key => $value) {
            $this->$key = $value;
        }

        $this->data_enturmacao = Portabilis_Date_Utils::brToPgSQL($this->data_enturmacao);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_matricula_lst.php');

        $this->breadcrumb('Enturmação da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        //nova lógica
        $retorno = false;
        if (is_numeric($this->ref_cod_matricula)) {
            if ($this->ref_cod_turma_origem == 'remover-enturmacao-destino') {
                $retorno = $this->removerEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma_destino);
            } elseif (!is_numeric($this->ref_cod_turma_origem)) {
                $retorno = $this->novaEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma_destino);
            } else {
                $retorno = $this->transferirEnturmacao(
                    $this->ref_cod_matricula,
                    $this->ref_cod_turma_origem,
                    $this->ref_cod_turma_destino
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
                $this->simpleRedirect('educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula);
            }
        } else {
            $this->simpleRedirect('/intranet/educar_aluno_lst.php');
        }
    }

    public function novaEnturmacao($matriculaId, $turmaDestinoId, $turnoId = null)
    {
        if (!$this->validaDataEnturmacao($matriculaId, $turmaDestinoId)) {
            return false;
        }

        $availableTimeService = new AvailableTimeService();

        $registration = LegacyRegistration::find($matriculaId);

        if ($this->validarCamposObrigatoriosCenso() && !$availableTimeService->isAvailable($registration->ref_cod_aluno, $turmaDestinoId)) {
            $this->mensagem = 'O aluno já está matriculado em uma turma com esse horário.';

            return false;
        }

        $enturmacaoExists = new clsPmieducarMatriculaTurma();
        $enturmacaoExists = $enturmacaoExists->lista(
            $matriculaId,
            $turmaDestinoId,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $enturmacaoExists = is_array($enturmacaoExists) && count($enturmacaoExists) > 0;

        if ($enturmacaoExists) {
            return false;
        }

        $enturmacao = new clsPmieducarMatriculaTurma(
            $matriculaId,
            $turmaDestinoId,
            $this->pessoa_logada,
            $this->pessoa_logada,
            null,
            null,
            1
        );

        $enturmacao->data_enturmacao = $this->data_enturmacao;

        $enturmacao->turno_id = $turnoId;
        $this->atualizaUltimaEnturmacao($matriculaId);

        return $enturmacao->cadastra();
    }

    public function validaDataEnturmacao($matriculaId, $turmaDestinoId, $transferir = false)
    {
        $dataObj = new \DateTime($this->data_enturmacao . ' 23:59:59');
        $matriculaObj = new clsPmieducarMatricula();
        $enturmacaoObj = new clsPmieducarMatriculaTurma();
        $dataAnoLetivoInicio = $matriculaObj->pegaDataAnoLetivoInicio($turmaDestinoId);
        $dataAnoLetivoFim = $matriculaObj->pegaDataAnoLetivoFim($turmaDestinoId);
        $exclusaoEnturmacao = $enturmacaoObj->getDataExclusaoUltimaEnturmacao($matriculaId);
        $maiorDataEnturmacao = $enturmacaoObj->getMaiorDataEnturmacao($matriculaId);
        $dataSaidaDaTurma = !empty($exclusaoEnturmacao)
            ? new \DateTime($exclusaoEnturmacao)
            : null;

        $maiorDataEnturmacao = !empty($maiorDataEnturmacao)
            ? new \DateTime($maiorDataEnturmacao)
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
        if (!$this->validaDataEnturmacao($matriculaId, $turmaDestinoId, true)) {
            return false;
        }

        $turnoId = null;

        if ($this->isTurmaIntegral($turmaDestinoId)) {
            $sequencialEnturmacaoAnterior = $this->getSequencialEnturmacaoByTurmaId($matriculaId, $turmaOrigemId);
            $enturmacao = new clsPmieducarMatriculaTurma;
            $enturmacao->ref_cod_matricula = $matriculaId;
            $enturmacao->ref_cod_turma = $turmaOrigemId;
            $enturmacao->sequencial = $sequencialEnturmacaoAnterior;
            $dadosEnturmacaoAnterior = $enturmacao->detalhe();
            $turnoId = $dadosEnturmacaoAnterior['turno_id'];
        }

        if ($this->removerEnturmacao($matriculaId, $turmaOrigemId, true)) {
            return $this->novaEnturmacao($matriculaId, $turmaDestinoId, $turnoId);
        }

        return false;
    }

    /**
     * Retorna a data base de remanejamento para a instituição.
     *
     * @param int $instituicao
     *
     * @return string|null
     */
    public function getDataBaseRemanejamento($instituicao)
    {
        $instituicao = new clsPmieducarInstituicao($instituicao);

        $instituicao = $instituicao->detalhe();

        return $instituicao['data_base_remanejamento'];
    }

    public function removerEnturmacao($matriculaId, $turmaId, $remanejado = false)
    {
        if (!$this->data_enturmacao) {
            $this->data_enturmacao = date('Y-m-d');
        }

        $sequencialEnturmacao = $this->getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId);
        $enturmacao = new clsPmieducarMatriculaTurma(
            $matriculaId,
            $turmaId,
            $this->pessoa_logada,
            null,
            null,
            $this->data_enturmacao,
            0,
            null,
            $sequencialEnturmacao
        );
        $detEnturmacao = $enturmacao->detalhe();

        $detEnturmacao = $detEnturmacao['data_enturmacao'];
        $enturmacao->data_enturmacao = $detEnturmacao;

        $instituicao = $enturmacao->getInstituicao($matriculaId);
        $instituicao = new clsPmieducarInstituicao($instituicao);
        $det_instituicao = $instituicao->detalhe();
        $data_base_remanejamento = $det_instituicao['data_base_remanejamento'];

        if (($data_base_remanejamento > $this->data_enturmacao) || (!$data_base_remanejamento)) {
            $enturmacao->removerSequencial = true;
        }

        if ($enturmacao->edita()) {
            if ($remanejado) {
                $enturmacao->marcaAlunoRemanejado($this->data_enturmacao);
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

        if ($db->execPreparedQuery($sql, [$matriculaId, $turmaId]) != false) {
            $db->ProximoRegistro();
            $sequencial = $db->Tupla();

            return $sequencial[0];
        }

        return 1;
    }

    public function atualizaUltimaEnturmacao($matriculaId)
    {
        $objMatriculaTurma = new clsPmieducarMatriculaTurma($matriculaId);
        $ultima_turma = $objMatriculaTurma->getUltimaTurmaEnturmacao($matriculaId);
        $sequencial = $objMatriculaTurma->getMaxSequencialEnturmacao($matriculaId);
        $lst_ativo = $objMatriculaTurma->lista($matriculaId, $ultima_turma, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $sequencial);

        $ativo = $lst_ativo[0]['ativo'];
        $data_exclusao = $lst_ativo[0]['data_exclusao'];

        $dataBaseRemanejamento = $this->getDataBaseRemanejamento(
            $objMatriculaTurma->getInstituicao()
        );

        $marcarAlunoComoRemanejado = is_null($dataBaseRemanejamento) || strtotime($dataBaseRemanejamento) < strtotime(date('Y-m-d'));

        if ($sequencial >= 1 && $marcarAlunoComoRemanejado) {
            $remanejado = true;
            $enturmacao = new clsPmieducarMatriculaTurma(
                $matriculaId,
                $ultima_turma,
                $this->pessoa_logada,
                $this->pessoa_logada,
                null,
                $data_exclusao,
                $ativo,
                null,
                $sequencial,
                null,
                null,
                null,
                $remanejado
            );

            return $enturmacao->edita();
        }

        return false;
    }

    public function Gerar()
    {
        die;
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
        $turma = new clsPmieducarTurma($turmaId);
        $turma = $turma->detalhe();

        return $turma['turma_turno_id'] == clsPmieducarTurma::TURNO_INTEGRAL;
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
