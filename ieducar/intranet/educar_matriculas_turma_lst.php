<?php

return new class extends clsListagem
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $ref_cod_turma;

    public $ref_ref_cod_serie;

    public $ref_cod_escola;

    public $ref_ref_cod_escola;

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ano;

    public function Gerar()
    {
        $this->titulo = 'Matrículas Turma - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Ano',
            'Turma',
            'Série',
            'Curso',
            'Escola',
        ];

        $this->addCabecalhos(coluna: $lista_busca);

        $this->inputsHelper()->dynamic(helperNames: ['ano', 'instituicao'], inputOptions: ['required' => true]);
        $this->inputsHelper()->dynamic(helperNames: ['escola', 'curso', 'serie', 'turma'], inputOptions: ['required' => false]);

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        // Paginador
        $this->limite = 20;
        $this->offset = 0;

        if (isset($_GET["pagina_{$this->nome}"])) {
            $this->offset = $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite;
        }

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby(strNomeCampo: 'nm_turma ASC');
        $obj_turma->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        if (empty($this->ano)) {
            $this->ano = date(format: 'Y');
        }

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $obj_turma->codUsuario = $this->pessoa_logada;
        }

        $lista = $obj_turma->lista3(
            int_cod_turma: $this->ref_cod_turma,
            int_ref_ref_cod_serie: $this->ref_cod_serie,
            int_ref_ref_cod_escola: $this->ref_ref_cod_escola,
            int_ativo: 1,
            int_ref_cod_curso: $this->ref_cod_curso,
            int_ref_cod_instituicao: $this->ref_cod_instituicao,
            ano: $this->ano
        );

        $total = $obj_turma->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_escola = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $registro['nm_escola'] = $det_ref_cod_escola['nome'];

                $link = route(name: 'enrollments.batch.enroll.index', parameters: ['schoolClass' => $registro['cod_turma']]);

                $lista_busca = [
                    "<a href=\"{$link}\">{$registro['ano']}</a>",
                    "<a href=\"{$link}\">{$registro['nm_turma']}</a>",
                ];

                if ($registro['ref_ref_cod_serie']) {
                    $lista_busca[] = "<a href=\"{$link}\">{$registro['nm_serie']}</a>";
                } else {
                    $lista_busca[] = "<a href=\"{$link}\">-</a>";
                }

                $lista_busca[] = "<a href=\"{$link}\">{$registro['nm_curso']}</a>";

                if ($registro['ref_ref_cod_escola']) {
                    $lista_busca[] = "<a href=\"{$link}\">{$registro['nm_escola']}</a>";
                } else {
                    $lista_busca[] = "<a href=\"{$link}\">-</a>";
                }

                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_matriculas_turma_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de turmas para enturmações', breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-matriculas-turma-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Matrículas Turmas';
        $this->processoAp = '659';
    }
};
