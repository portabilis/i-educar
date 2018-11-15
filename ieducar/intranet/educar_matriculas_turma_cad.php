<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Matriculas Turma');
        $this->processoAp = 659;
        $this->addEstilo('localizacaoSistema');
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

    public $sequencial;

    public $ref_cod_instituicao;

    public $ref_ref_cod_escola;

    public $ref_cod_curso;

    public $ref_ref_cod_serie;

    public $ref_cod_turma;

    public $matriculas_turma;

    public $incluir_matricula;

    public $data_enturmacao;

    public $check_desenturma;

    public function Inicializar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->ano = $_GET['ano'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            659,
            $this->pessoa_logada,
            7,
            'educar_matriculas_turma_lst.php'
        );

        if (is_numeric($this->ref_cod_turma)) {
            $obj_turma = new clsPmieducarTurma();
            $lst_turma = $obj_turma->lista($this->ref_cod_turma);

            if (is_array($lst_turma)) {
                $registro = array_shift($lst_turma);
            }

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }

            $this->url_cancelar = $retorno == 'Editar'
                ? sprintf('educar_matriculas_turma_det.php?ref_cod_matricula=%d&ref_cod_turma=%d', $this->ref_cod_matricula, $this->ref_cod_turma)
                : 'educar_matriculas_turma_lst.php';

            $this->nome_url_cancelar = 'Cancelar';

            $db = new clsBanco();
            $existe_entrumacao = $db->CampoUnico("select * from matricula_turma where ref_cod_turma = $this->ref_cod_turma");

            if ($retorno == 'Editar' and $existe_entrumacao) {
                $this->url_copiar_enturmacoes = sprintf('educar_matricula_cad.php?ref_cod_turma_copiar_enturmacoes=%d', $this->ref_cod_turma);
                $this->nome_url_copiar_enturmacoes = 'Copiar enturmações';
            }

            $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

            $localizacao = new LocalizacaoSistema();
            $localizacao->entradaCaminhos([
                $_SERVER['SERVER_NAME'] . '/intranet' => 'In&iacute;cio',
                'educar_index.php' => 'Escola',
                '' => "{$nomeMenu} matr&iacute;culas da turma"
            ]);
            $this->enviaLocalizacao($localizacao->montar());

            return $retorno;
        }

        header('Location: educar_matriculas_turma_lst.php');
        die;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = $this->$campo ? $this->$campo : $val;
            }
        }

        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);
        $this->campoOculto('ref_ref_cod_escola', $this->ref_ref_cod_escola);
        $this->campoOculto('ref_ref_cod_serie', $this->ref_ref_cod_serie);
        $this->campoOculto('ref_cod_curso', $this->ref_cod_curso);

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $obj_cod_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $nm_instituicao = $obj_cod_instituicao_det['nm_instituicao'];
            $this->campoRotulo('nm_instituicao', 'Institui&ccedil;&atilde;o', $nm_instituicao);
        }

        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($this->ref_ref_cod_escola) {
                $obj_ref_cod_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola['nome'];
                $this->campoRotulo('nm_escola', 'Escola', $nm_escola);
            }
        }

        if ($this->ref_cod_curso) {
            $obj_ref_cod_curso = new clsPmieducarCurso($this->ref_cod_curso);
            $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
            $nm_curso = $det_ref_cod_curso['nm_curso'];
            $this->campoRotulo('nm_curso', 'Curso', $nm_curso);
        }

        if ($this->ref_ref_cod_serie) {
            $obj_ref_cod_serie = new clsPmieducarSerie($this->ref_ref_cod_serie);
            $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
            $nm_serie = $det_ref_cod_serie['nm_serie'];
            $this->campoRotulo('nm_serie', 'S&eacute;rie', $nm_serie);

            // busca o ano em q a escola esta em andamento
            $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
            $lst_ano_letivo = $obj_ano_letivo->lista(
                $this->ref_ref_cod_escola,
                null,
                null,
                null,
                1,
                null,
                null,
                null,
                null,
                1
            );

            if (is_array($lst_ano_letivo)) {
                $det_ano_letivo = array_shift($lst_ano_letivo);
                $ano_letivo = $det_ano_letivo['ano'];
            } else {
                $this->mensagem = 'N&acirc;o foi possÃ­vel encontrar o ano letivo em andamento da escola.';

                return false;
            }
        }

        if ($this->ref_cod_turma) {
            $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
            $det_turma = $obj_turma->detalhe();
            $nm_turma = $det_turma['nm_turma'];
            $this->campoRotulo('nm_turma', 'Turma', $nm_turma);
        }

        if ($this->ano) {
            $this->campoRotulo('ano', 'Ano', $this->ano);
        }

        // Inlui o aluno
        $this->campoQuebra();

        if ($_POST['matriculas_turma']) {
            $this->matriculas_turma = unserialize(urldecode($_POST['matriculas_turma']));
        }

        $alunosEnturmados = false;

        if (is_numeric($this->ref_cod_turma) && !$_POST) {
            $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
            $obj_matriculas_turma->setOrderby('sequencial_fechamento, nome_aluno');
            $lst_matriculas_turma = $obj_matriculas_turma->lista(
                null,
                $this->ref_cod_turma,
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
                [1, 2, 3],
                null,
                null,
                $this->ano,
                null,
                true,
                null,
                1,
                true
            );

            if (is_array($lst_matriculas_turma)) {
                $alunosEnturmados = true;
                foreach ($lst_matriculas_turma as $key => $campo) {
                    $this->matriculas_turma[$campo['ref_cod_matricula']]['sequencial_'] = $campo['sequencial'];
                }
            }
        }

        if ($_POST['ref_cod_matricula']) {
            $obj_matriculas_turma = new clsPmieducarMatriculaTurma(
                $_POST['ref_cod_matricula'],
                $this->ref_cod_turma
            );

            $sequencial = $obj_matriculas_turma->buscaSequencialMax();

            $this->matriculas_turma[$_POST['ref_cod_matricula']]['sequencial_'] = $sequencial;

            unset($this->ref_cod_matricula);
        }

        if ($this->matriculas_turma) {
            $this->campoRotulo('tituloUm', 'Matr&iacute;culas', '<b>&nbsp;Alunos j&aacute; matriculados e enturmados&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Marque o(s) aluno(s) para desenturmar</b><label style=\'display: block; width: 350px; margin-left: 256px;\'>&nbsp;&nbsp;&nbsp;<input type=\'checkbox\' name=\'CheckTodos\' onClick=\'marcarCheck(' . '"check_desenturma[]"' . ');\'/>Marcar todos</label>');
            foreach ($this->matriculas_turma as $matricula => $campo) {
                $obj_matricula = new clsPmieducarMatricula($matricula);
                $det_matricula = $obj_matricula->detalhe();

                $obj_aluno = new clsPmieducarAluno();
                $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
                $det_aluno = array_shift($lst_aluno);
                $nm_aluno = $det_aluno['nome_aluno'];

                $this->campoTextoInv(
                    'ref_cod_matricula_' . $matricula,
                    '',
                    $nm_aluno,
                    40,
                    255,
                    false,
                    false,
                    true,
                    '',
                    '',
                    '',
                    '',
                    'ref_cod_matricula'
                );

                $this->campoCheck('check_desenturma[' . $matricula . ']', '', $matricula);
            }
        }

        $this->campoOculto('matriculas_turma', serialize($this->matriculas_turma));

        // Aluno
        $opcoes = [];
        $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
        $alunos = $obj_matriculas_turma->alunosNaoEnturmados(
            $this->ref_ref_cod_escola,
            $this->ref_ref_cod_serie,
            $this->ref_cod_curso,
            $this->ano
        );

        if (is_array($alunos)) {
            for ($i = 0; $i < count($alunos); $i++) {
                $obj_matricula = new clsPmieducarMatricula($alunos[$i]);
                $det_matricula = $obj_matricula->detalhe();

                $obj_aluno = new clsPmieducarAluno();
                $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
                $det_aluno = array_shift($lst_aluno);

                $opcoes[$alunos[$i]] = $det_aluno['nome_aluno'];
            }
        }

        if (count($opcoes)) {
            asort($opcoes);

            $this->inputsHelper()->date('data_enturmacao', ['label' => 'Data da enturmação']);
            $this->campoRotulo('tituloDois', 'Matrículas', '<b>&nbsp;Alunos já matriculados e não enturmados&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Marque o(s) aluno(s) para enturmar</b><label style=\'display: block; width: 350px; margin-left: 256px;\'>&nbsp;&nbsp;&nbsp;<input checked type=\'checkbox\' name=\'CheckTodosDois\' onClick=\'marcarCheckDois(' . '"ref_cod_matricula[]"' . ');\'/>Marcar todos</label>');

            foreach ($opcoes as $key => $aluno) {
                $this->campoRotulo($key, '', '<table  style="font-size:11px; font-family: arial, verdana, lucida, helvetica, sans-serif;" border="0px"><tr><td width="258px">' . $aluno . '</td><td><input value="$key" type="checkbox" name="ref_cod_matricula[' . $key . ']" id="ref_cod_matricula[]"></td></tr></table>', '');
            }

        } elseif ($alunosEnturmados) {
            $this->campoRotulo('rotulo_1', '-', 'Todos os alunos matriculados na série já se encontram enturmados.');
        } else {
            $this->campoRotulo('rotulo_1', '-', 'Não há alunos enturmados.');
        }

        $this->campoQuebra();
    }

    public function Novo()
    {
    }

    public function Editar()
    {
        $this->data_enturmacao = Portabilis_Date_Utils::brToPgSQL($this->data_enturmacao);

        foreach ($this->check_desenturma as $matricula) {
            $this->removerEnturmacao($matricula, $this->ref_cod_turma);
        }

        if (empty($this->matriculas_turma)) {
            $this->simpleRedirect('educar_matriculas_turma_lst.php');

            return false;
        }

        $objTurma = new clsPmieducarTurma();
        $objEnturmacoes = new clsPmieducarMatriculaTurma();
        $objEscolaSerie = new clsPmieducarEscolaSerie();

        $totalAlunosParaEnturmar = count($this->ref_cod_matricula);
        $dadosTurma = $objTurma->lista($this->ref_cod_turma);
        $maxAlunos = $dadosTurma[0]['max_aluno'];
        $alunosEnturmados = $objEnturmacoes->enturmacoesSemDependencia($this->ref_cod_turma);
        $vagasDisponiveis = $maxAlunos - $alunosEnturmados[0];
        $dadosEscolaSerie = $objEscolaSerie->lista($this->ref_ref_cod_escola, $this->ref_ref_cod_serie);
        $bloquearEnturmacaoSeNaoHouverVagas = $dadosEscolaSerie[0]['bloquear_enturmacao_sem_vagas'];

        if ($vagasDisponiveis < $totalAlunosParaEnturmar && $bloquearEnturmacaoSeNaoHouverVagas) {

            if ($vagasDisponiveis > 0) {
                $this->mensagem = 'Cadastro não realizado. Há apenas ' . $vagasDisponiveis . ' vagas restantes para esta turma.';
            } else {
                $this->mensagem = 'Cadastro não realizado. Não há mais vagas disponíveis para esta turma.';
            }

            return false;
        }

        foreach ($this->ref_cod_matricula as $matricula => $campo) {
            $enturmacao = new clsPmieducarMatriculaTurma(
                $matricula,
                $this->ref_cod_turma,
                null,
                $this->pessoa_logada,
                null,
                null,
                1,
                null,
                $campo['sequencial_']
            );

            if ($enturmacao->existeEnturmacaoAtiva()) {
                continue;
            }

            $ultimaDataSaida = $enturmacao->getDataSaidaEnturmacaoAnterior(
                $matricula, $enturmacao->buscaSequencialMax()
            );

            $permiteEnturmar = empty($ultimaDataSaida) || $this->data_enturmacao >= $ultimaDataSaida;

            $enturmacao->data_enturmacao = $this->data_enturmacao;

            if ($permiteEnturmar && $enturmacao->cadastra()) {
                continue;
            }

            // FIXME resolver problema de XSS (Chrome bloqueia página)

            header('X-XSS-Protection: 0');

            // TODO rollback de todas as enturmações se houver erro

            if ($permiteEnturmar) {
                $this->mensagem .= 'Cadastro não realizado.<br>';
            } else {
                $this->mensagem .= 'A data de enturmação é anterior a data de saída da última enturmação.<br>';
            }

            return false;
        }

        $this->simpleRedirect('educar_matriculas_turma_lst.php');
    }

    public function Excluir()
    {
    }

    public function removerEnturmacao($matriculaId, $turmaId)
    {
        $sequencialEnturmacao = $this->getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId);
        $enturmacao = new clsPmieducarMatriculaTurma(
            $matriculaId,
            $turmaId,
            $this->pessoa_logada,
            null,
            null,
            date('Y-m-d'),
            0,
            null,
            $sequencialEnturmacao
        );
        if ($enturmacao->edita()) {
            $enturmacao->marcaAlunoRemanejado($this->data_enturmacao);

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
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script type="text/javascript">

    function fixUpCheckBoxes() {
        $j('input[name^=check_desenturma]').each(function (index, element) {
            element.id = 'check_desenturma[]';
            element.checked = false;
        });
    }

    fixUpCheckBoxes();

    function marcarCheck(idValue) {
        var contaForm = document.formcadastro.elements.length;
        var campo = document.formcadastro;
        var i;
        for (i = 0; i < contaForm; i++) {
            if (campo.elements[i].id == idValue) {
                campo.elements[i].checked = campo.CheckTodos.checked;
            }
        }
    }

    function fixUpCheckBoxesDois() {
        $j('input[name^=ref_cod_matricula]').each(function (index, element) {
            element.id = 'ref_cod_matricula[]';
            element.checked = true;
        });
    }

    fixUpCheckBoxesDois();

    function marcarCheckDois(idValueDois) {
        var contaFormDois = document.formcadastro.elements.length;
        var campoDois = document.formcadastro;
        var i;
        for (i = 0; i < contaFormDois; i++) {
            if (campoDois.elements[i].id == idValueDois) {
                campoDois.elements[i].checked = campoDois.CheckTodosDois.checked;
            }
        }
    }
    
</script>
