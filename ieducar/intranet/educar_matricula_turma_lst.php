<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
        $this->processoAp = 578;
    }
}

class indice extends clsListagem
{
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $ref_cod_matricula;
    public $ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_serie;
    public $ref_cod_escola;
    public $ref_cod_turma_origem;
    public $ref_cod_curso;
    public $ref_cod_instituicao;
    public $ano_letivo;
    public $sequencial;

    public function Gerar()
    {
        $this->titulo = 'Selecione uma turma para enturmar ou remover a enturmação';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        if (!$this->ref_cod_matricula) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $this->ref_cod_curso = $det_matricula['ref_cod_curso'];

        $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->ano_letivo = $_GET['ano_letivo'];

        $this->addCabecalhos([
            'Turma',
            'Enturmado'
        ]);

        // Busca dados da matricula
        $obj_ref_cod_matricula = new clsPmieducarMatricula();
        $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

        $obj_aluno = new clsPmieducarAluno();
        $det_aluno = array_shift($obj_aluno->lista(
            $detalhe_aluno['ref_cod_aluno'],
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

        // Filtros de foreign keys
        $opcoes = ['' => 'Selecione'];

        // Opções de turma
        $objTemp = new clsPmieducarTurma();
        $lista = $objTemp->lista3(
            null,
            null,
            null,
            $this->ref_cod_serie,
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
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_curso,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ano_letivo
        );

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['cod_turma']] = $registro['nm_turma'];
            }

            $this->exibirBotaoSubmit = false;
        }

        #$this->campoLista('ref_cod_turma_', 'Turma', $opcoes, $this->ref_cod_turma);

        // outros filtros
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);
        $this->campoOculto('ref_cod_serie', '');
        $this->campoOculto('ref_cod_turma', '');
        $this->campoOculto('ref_cod_escola', '');
        $this->campoOculto('ano_letivo', $this->ano_letivo);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_matricula_turma = new clsPmieducarTurma();
        $obj_matricula_turma->setOrderby(' t.nm_turma ASC');
        $obj_matricula_turma->setLimite($this->limite, $this->offset);

        $lista = $obj_matricula_turma->lista3(
            $this->ref_cod_turma,
            null,
            null,
            $this->ref_cod_serie,
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
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_curso,
            null,
            null,
            null,
            null,
            null,
            null,
            true,
            null,
            null,
            $this->ano_letivo
        );

        if (is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_curso) &&
            is_numeric($this->ref_cod_escola)) {
            $sql = "
                SELECT
                  t.cod_turma, t.ref_usuario_exc, t.ref_usuario_cad, t.ref_ref_cod_serie,
                  t.ref_ref_cod_escola, t.ref_cod_infra_predio_comodo, t.nm_turma, t.sgl_turma,
                  t.max_aluno, t.multiseriada, t.data_cadastro, t.data_exclusao, t.ativo,
                  t.ref_cod_turma_tipo, t.hora_inicial, t.hora_final, t.hora_inicio_intervalo,
                  t.hora_fim_intervalo, t.ref_cod_regente, t.ref_cod_instituicao_regente,
                  t.ref_cod_instituicao, t.ref_cod_curso, t.ref_ref_cod_serie_mult,
                  t.ref_ref_cod_escola_mult
                FROM
                  pmieducar.turma t
                WHERE
                  t.ref_ref_cod_serie_mult = {$this->ref_cod_serie}
                  AND t.ref_ref_cod_escola={$this->ref_cod_escola}
                  AND t.ativo = '1'
                  AND t.ref_ref_cod_escola = '{$this->ref_cod_escola}'
            ";

            $db = new clsBanco();
            $db->Consulta($sql);

            $lista_aux = [];
            while ($db->ProximoRegistro()) {
                $lista_aux[] = $db->Tupla();
            }

            if (is_array($lista_aux) && count($lista_aux)) {
                if (is_array($lista) && count($lista)) {
                    $lista = array_merge($lista, $lista_aux);
                } else {
                    $lista = $lista_aux;
                }
            }
        }
        $total = $obj_matricula_turma->_total;

        $enturmacoesMatricula = new clsPmieducarMatriculaTurma();
        $enturmacoesMatricula = $enturmacoesMatricula->lista3(
            $this->ref_cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ano_letivo
        );

        $turmasThisSerie = $lista;
        // lista turmas disponiveis para enturmacao, somente lista as turmas sem enturmacao
        foreach ($turmasThisSerie as $turma) {
            $turmaHasEnturmacao = false;
            foreach ($enturmacoesMatricula as $enturmacao) {
                if (!$turmaHasEnturmacao && $turma['cod_turma'] == $enturmacao['ref_cod_turma']) {
                    $turmaHasEnturmacao = true;
                }
            }

            if ($turmaHasEnturmacao) {
                $enturmado = 'Sim';
            } else {
                $enturmado = 'Não';
            }

            $link = route('enrollments.enroll.create', [
                'registration' => $this->ref_cod_matricula,
                'schoolClass' => $turma['cod_turma'],
            ]);
            $this->addLinhas(["<a href='{$link}'>{$turma['nm_turma']}</a>", $enturmado]);
        }

        $this->addPaginador2(
            'educar_matricula_turma_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        $this->largura = '100%';

        $this->breadcrumb('Enturmações da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
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
?>
<script type="text/javascript">
    function enturmar(ref_cod_escola, ref_cod_serie, ref_cod_matricula, ref_cod_turma, ano_letivo) {
        document.formcadastro.method = 'post';
        document.formcadastro.action = 'educar_matricula_turma_det.php';

        document.formcadastro.ref_cod_escola.value = ref_cod_escola;
        document.formcadastro.ref_cod_serie.value = ref_cod_serie;
        document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
        document.formcadastro.ref_cod_turma.value = ref_cod_turma;
        document.formcadastro.ano_letivo.value = ano_letivo;

        document.formcadastro.submit();
    }
</script>
