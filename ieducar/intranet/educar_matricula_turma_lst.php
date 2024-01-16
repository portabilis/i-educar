<?php

return new class extends clsListagem
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

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 683);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Gerar()
    {
        $this->titulo = 'Selecione uma turma para enturmar ou remover a enturmação';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        if (!$this->ref_cod_matricula) {
            $this->simpleRedirect(url: 'educar_matricula_lst.php');
        }

        $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $this->ref_cod_curso = $det_matricula['ref_cod_curso'];

        $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
        $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->ano_letivo = $_GET['ano_letivo'];

        $this->addCabecalhos(coluna: [
            'Turma',
            'Enturmado',
        ]);

        // Busca dados da matricula
        $obj_ref_cod_matricula = new clsPmieducarMatricula();
        $matricula = $obj_ref_cod_matricula->lista(int_cod_matricula: $this->ref_cod_matricula);
        $detalhe_aluno = array_shift(array: $matricula);

        $obj_aluno = (new clsPmieducarAluno())->lista(
            int_cod_aluno: $detalhe_aluno['ref_cod_aluno'],
            int_ativo: 1
        );

        $det_aluno = array_shift(array: $obj_aluno);
        $obj_escola = new clsPmieducarEscola(
            cod_escola: $this->ref_cod_escola,
            bloquear_lancamento_diario_anos_letivos_encerrados: 1
        );

        $det_escola = $obj_escola->detalhe();

        if ($det_escola['nome']) {
            $this->campoRotulo(nome: 'nm_escola', campo: 'Escola', valor: $det_escola['nome']);
        }

        $this->campoRotulo(nome: 'nm_pessoa', campo: 'Nome do Aluno', valor: $det_aluno['nome_aluno']);

        // Filtros de foreign keys
        $opcoes = ['' => 'Selecione'];

        // Opções de turma
        $objTemp = new clsPmieducarTurma();
        $lista = $objTemp->lista3(
            int_ref_ref_cod_serie: $this->ref_cod_serie,
            int_ref_ref_cod_escola: $this->ref_cod_escola,
            int_ref_cod_curso: $this->ref_cod_curso,
            ano: $this->ano_letivo
        );

        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['cod_turma']] = $registro['nm_turma'];
            }

            $this->exibirBotaoSubmit = false;
        }

        // outros filtros
        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);
        $this->campoOculto(nome: 'ref_cod_serie', valor: '');
        $this->campoOculto(nome: 'ref_cod_turma', valor: '');
        $this->campoOculto(nome: 'ref_cod_escola', valor: '');
        $this->campoOculto(nome: 'ano_letivo', valor: $this->ano_letivo);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_matricula_turma = new clsPmieducarTurma();
        $obj_matricula_turma->setOrderby(strNomeCampo: ' t.nm_turma ASC');
        $obj_matricula_turma->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_matricula_turma->lista3(
            int_cod_turma: $this->ref_cod_turma,
            int_ref_ref_cod_serie: $this->ref_cod_serie,
            int_ref_ref_cod_escola: $this->ref_cod_escola,
            int_ativo: 1,
            int_ref_cod_curso: $this->ref_cod_curso,
            visivel: true,
            ano: $this->ano_letivo
        );

        if (is_numeric(value: $this->ref_cod_serie) && is_numeric(value: $this->ref_cod_curso) &&
            is_numeric(value: $this->ref_cod_escola)) {
            $sql = "
                SELECT
                  t.cod_turma, t.ref_usuario_exc, t.ref_usuario_cad, t.ref_ref_cod_serie,
                  t.ref_ref_cod_escola, t.nm_turma, t.sgl_turma,
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
            $db->Consulta(consulta: $sql);

            $lista_aux = [];
            while ($db->ProximoRegistro()) {
                $lista_aux[] = $db->Tupla();
            }

            if (is_array(value: $lista_aux) && count(value: $lista_aux)) {
                if (is_array(value: $lista) && count(value: $lista)) {
                    $lista = array_merge($lista, $lista_aux);
                } else {
                    $lista = $lista_aux;
                }
            }
        }
        $total = $obj_matricula_turma->_total;

        $enturmacoesMatricula = (new clsPmieducarMatriculaTurma())->lista3(
            int_ref_cod_matricula: $this->ref_cod_matricula,
            int_ativo: 1,
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

            $link = route(name: 'enrollments.enroll.create', parameters: [
                'registration' => $this->ref_cod_matricula,
                'schoolClass' => $turma['cod_turma'],
            ]);
            $this->addLinhas(linha: ["<a href='{$link}'>{$turma['nm_turma']}</a>", $enturmado]);
        }

        $this->addPaginador2(
            strUrl: 'educar_matricula_turma_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Enturmações da matrícula', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-matricula-turma-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Matricula Turma';
        $this->processoAp = 578;
    }
};
