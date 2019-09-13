<?php

use App\Process;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'App/Model/MatriculaSituacao.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo('Histórico de enturmações da matrícula');

        $this->processoAp = Process::ENROLLMENT_HISTORY;
    }
}

class indice extends clsListagem
{
    public $ref_cod_matricula;

    public function Gerar()
    {
        $this->titulo = 'Lista de enturmações da matrícula';

        $this->exibirBotaoSubmit = false;

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        if (!$this->ref_cod_matricula) {
            $this->simpleRedirect('educar_matricula_historico_lst.php');
        }

        $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        $situacao = App_Model_MatriculaSituacao::getSituacao($det_matricula['aprovado']);

        $this->ref_cod_curso = $det_matricula['ref_cod_curso'];

        $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->ano_letivo = $_GET['ano_letivo'];

        $this->addCabecalhos([
            'Sequencial',
            'Turma',
            'Turno do aluno',
            'Ativo',
            'Data de enturmação',
            'Data de saída',
            'Transferido',
            'Remanejado',
            'Reclassificado',
            'Abandono',
            'Falecido',
            'Usuário criou',
            'Usuário editou'
        ]);

        // Busca dados da matricula
        $obj_ref_cod_matricula = new clsPmieducarMatricula();
        $detalhe_matricula = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

        $obj_aluno = new clsPmieducarAluno();
        $det_aluno = array_shift($obj_aluno->lista(
            $detalhe_matricula['ref_cod_aluno'],
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        ));

        $obj_escola = new clsPmieducarEscola(
            $this->ref_cod_escola,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );
        $det_escola = $obj_escola->detalhe();

        if ($det_escola['nome']) {
            $this->campoRotulo('nm_escola', 'Escola', $det_escola['nome']);
        }

        $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $det_aluno['nome_aluno']);
        $this->campoRotulo('matricula', 'Matrícula', $this->ref_cod_matricula);
        $this->campoRotulo('situacao', 'Situação', $situacao);
        $this->campoRotulo('data_saida', 'Data saída', dataToBrasil($detalhe_matricula['data_cancel']));

        //Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj = new clsPmieducarMatriculaTurma();
        $obj->setOrderby('sequencial ASC');
        $obj->setLimite($this->limite, $this->offset);

        $lista = $obj->lista($this->ref_cod_matricula);

        $total = $obj->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $ativo = $registro['ativo'] ? 'Sim' : 'Não';
                $dataEnturmacao = dataToBrasil($registro['data_enturmacao']);
                $dataSaida = dataToBrasil($registro['data_exclusao']);
                $dataSaidaMatricula = dataToBrasil($detalhe_matricula['data_cancel']);
                $transferido = $registro['transferido'] ? 'Sim' : 'Não';
                $remanejado = $registro['remanejado'] ? 'Sim' : 'Não';
                $abandono = $registro['abandono'] ? 'Sim' : 'Não';
                $reclassificado = $registro['reclassificado'] ? 'Sim' : 'Não';
                $falecido = $registro['falecido'] ? 'Sim' : 'Não';

                $usuarioCriou = new clsPessoa_($registro['ref_usuario_cad']);
                $usuarioCriou = $usuarioCriou->detalhe();

                $usuarioEditou = new clsPessoa_($registro['ref_usuario_exc']);
                $usuarioEditou = $usuarioEditou->detalhe();

                $turno = '';
                if ($registro['turno_id']) {
                    $turno = Portabilis_Utils_Database::selectField('SELECT nome FROM pmieducar.turma_turno WHERE id = $1', [$registro['turno_id']]);
                }

                if ($this->user()->can('modify', Process::ENROLLMENT_HISTORY)) {
                    $this->addLinhas(
                        [
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$registro['sequencial']}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$registro['nm_turma']}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$turno}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$ativo}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$dataEnturmacao}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$dataSaida}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$transferido}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$remanejado}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$reclassificado}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$abandono}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$falecido}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$usuarioCriou['nome']}</a>",
                            "<a href=\"educar_matricula_historico_cad.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_turma={$registro['ref_cod_turma']}&sequencial={$registro['sequencial']}  \">{$usuarioEditou['nome']}</a>",
                        ]
                    );
                } else {
                    $this->addLinhas(
                        [
                            $registro['sequencial'],
                            $registro['nm_turma'],
                            $turno,
                            $ativo,
                            $dataEnturmacao,
                            $dataSaida,
                            $transferido,
                            $remanejado,
                            $reclassificado,
                            $abandono,
                            $falecido,
                            $usuarioCriou['nome'],
                            $usuarioEditou['nome'],
                        ]
                    );
                }
            }
        }

        $this->addLinhas('<small>A coluna "Turno do aluno" permanecerá em branco quando o turno do aluno for o mesmo da turma.</small>');

        $this->addPaginador2('educar_matricula_historico_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->acao = "go(\"educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}\")";
        $this->nome_acao = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb('Histórico de enturmações da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
