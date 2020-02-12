<?php

use App\Process;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'App/Model/MatriculaSituacao.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Bloqueio do ano letivo");

        $this->processoAp = Process::ENROLLMENT_HISTORY;
    }
}

class indice extends clsCadastro
{
    public $ref_cod_matricula;
    public $ref_cod_turma;
    public $sequencial;

    public function Inicializar()
    {
        $retorno = 'Editar';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->sequencial = $_GET['sequencial'];

        if ($this->user()->cannot('modify', Process::ENROLLMENT_HISTORY)) {
            $this->simpleRedirect("/enrollment-history/{$this->ref_cod_matricula}");
        }

        $this->fexcluir = $this->user()->can('remove', Process::ENROLLMENT_HISTORY);

        $this->breadcrumb('Histórico de enturmações da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
        $link = route('enrollments.enrollment-history', ['id' => $this->ref_cod_matricula]);
        $this->url_cancelar = $link;

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);

        $enturmacao = new clsPmieducarMatriculaTurma($this->ref_cod_matricula);
        $enturmacao->ref_cod_matricula = $this->ref_cod_matricula;
        $enturmacao->ref_cod_turma = $this->ref_cod_turma;
        $enturmacao->sequencial = $this->sequencial;
        $enturmacao = $enturmacao->detalhe();

        $matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
        $matricula = $matricula->detalhe();

        $instituicao = new clsPmieducarInstituicao($matricula['ref_cod_instituicao']);
        $instituicao = $instituicao->detalhe();

        $escola = new clsPmieducarEscola($matricula['ref_ref_cod_escola']);
        $escola = $escola->detalhe();

        $this->campoRotulo('ano', 'Ano', $matricula['ano']);
        $this->campoRotulo('nm_instituicao', 'Instituição', $instituicao['nm_instituicao']);
        $this->campoRotulo('nm_escola', 'Escola', $escola['nome']);
        $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $enturmacao['nome']);
        $this->campoRotulo('sequencial', 'Sequencial', $enturmacao['sequencial']);

        switch ($matricula['aprovado']) {
            case 1:
                $situacao = 'Aprovado';
                break;
            case 2:
                $situacao = 'Reprovado';
                break;
            case 3:
                $situacao = 'Cursando';
                break;
            case 4:
                $situacao = 'Transferido';
                break;
            case 5:
                $situacao = 'Reclassificado';
                break;
            case 6:
                $situacao = 'Abandono';
                break;
            case 7:
                $situacao = 'Em Exame';
                break;
            case 12:
                $situacao = 'Aprovado com dependência';
                break;
            case 13:
                $situacao = 'Aprovado pelo conselho';
                break;
            case 14:
                $situacao = 'Reprovado por faltas';
                break;
            default:
                $situacao = '';
                break;
        }

        $required = false;

        if (!$enturmacao['ativo']) {
            $required = true;
        }

        $this->campoRotulo('situacao', 'Situação', $situacao);
        $this->inputsHelper()->date('data_enturmacao', ['label' => 'Data enturmação', 'value' => dataToBrasil($enturmacao['data_enturmacao']), 'placeholder' => '']);
        $this->inputsHelper()->date('data_exclusao', ['label' => 'Data de saí­da', 'value' => dataToBrasil($enturmacao['data_exclusao']), 'placeholder' => '', 'required' => $required]);
    }

    public function Editar()
    {
        $enturmacao = new clsPmieducarMatriculaTurma();
        $enturmacao->ref_cod_matricula = $this->ref_cod_matricula;
        $enturmacao->ref_cod_turma = $this->ref_cod_turma;
        $enturmacao->sequencial = $this->sequencial;
        $enturmacao->ref_usuario_exc = $this->pessoa_logada;
        $enturmacao->data_enturmacao = dataToBanco($this->data_enturmacao);
        $enturmacao->data_exclusao = dataToBanco($this->data_exclusao);

        $dataSaidaEnturmacaoAnterior = $enturmacao->getDataSaidaEnturmacaoAnterior($this->ref_cod_matricula, $this->sequencial);
        $dataEntradaEnturmacaoSeguinte = $enturmacao->getDataEntradaEnturmacaoSeguinte($this->ref_cod_matricula, $this->sequencial);

        $matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
        $matricula = $matricula->detalhe();
        $dataSaidaMatricula = '';

        if ($matricula['data_cancel']) {
            $dataSaidaMatricula = date('Y-m-d', strtotime($matricula['data_cancel']));
        }

        $seqUltimaEnturmacao = $enturmacao->getUltimaEnturmacao($this->ref_cod_matricula);

        if ($enturmacao->data_exclusao && ($enturmacao->data_exclusao < $enturmacao->data_enturmacao)) {
            $this->mensagem = 'Edição não realizada. A data de saída não pode ser anterior a data de enturmação.';

            return false;
        }

        if ($enturmacao->data_exclusao && $dataEntradaEnturmacaoSeguinte && ($enturmacao->data_exclusao > $dataEntradaEnturmacaoSeguinte)) {
            $this->mensagem = 'Edição não realizada. A data de saída não pode ser posterior a data de entrada da enturmação seguinte.';

            return false;
        }

        if ($dataSaidaEnturmacaoAnterior && ($enturmacao->data_enturmacao < $dataSaidaEnturmacaoAnterior)) {
            $this->mensagem = 'Edição não realizada. A data de enturmação não pode ser anterior a data de saída da enturmação antecessora.';

            return false;
        }

        if (
            $dataSaidaMatricula
            && ($enturmacao->data_exclusao > $dataSaidaMatricula)
            && (
                App_Model_MatriculaSituacao::TRANSFERIDO == $matricula['aprovado']
                || App_Model_MatriculaSituacao::ABANDONO == $matricula['aprovado']
                || App_Model_MatriculaSituacao::RECLASSIFICADO == $matricula['aprovado']
            ) && ($this->sequencial == $seqUltimaEnturmacao)
        ) {
            $this->mensagem = 'Edição não realizada. A data de saída não pode ser posterior a data de saída da matricula.';

            return false;
        }

        $editou = $enturmacao->edita();

        if ($editou) {
            if (is_null($dataSaidaMatricula) || empty($dataSaidaMatricula)) {
                $dataSaidaMatricula = $enturmacao->data_exclusao;

                $matricula_get = new clsPmieducarMatricula(
                    $this->ref_cod_matricula,
                    null,
                    null,
                    null,
                    null,
                    $matricula['ref_usuario_cad'],
                    $matricula['ref_cod_aluno'],
                    $matricula['aprovado'],
                    null,
                    null,
                    null,
                    $matricula['ano'],
                    $matricula['ultima_matricula'],
                    null,
                    null,
                    null,
                    null,
                    $matricula['ref_cod_curso'],
                    null,
                    null,
                    null,
                    $dataSaidaMatricula,
                    null
                );
                $matricula_get->edita();
            }

            $this->mensagem = 'Edição efetuada com sucesso.';
            $this->simpleRedirect("/enrollment-history/{$this->ref_cod_matricula}");
        }

        $this->mensagem = 'Edição não realizada.';

        return false;
    }

    public function Excluir()
    {
        $enturmacao = new clsPmieducarMatriculaTurma();
        $enturmacao->ref_cod_matricula = $this->ref_cod_matricula;
        $enturmacao->ref_cod_turma = $this->ref_cod_turma;
        $enturmacao->sequencial = $this->sequencial;
        $enturmacao->ref_usuario_exc = $this->pessoa_logada;
        $enturmacao->data_exclusao = dataToBanco($this->data_exclusao);
        $excluiu = $enturmacao->excluir();

        if ($excluiu) {
            $this->mensagem = 'Exclusão efetuada com sucesso.';
            $this->simpleRedirect("/enrollment-history/{$this->ref_cod_matricula}");
        }

        $this->mensagem = 'Exclusão não realizada.';

        return false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
