<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $ano;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $turno;

    public function Gerar()
    {

        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

        $this->titulo = 'Vagas por série - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Ano', 'Escola', 'Curso', 'Série', 'Turno', 'Vagas'
        ]);

        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola', 'curso', 'serie'], ['required' => false]);

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        $get_escola = true;
        $get_escola_curso_serie = true;
        $sem_padrao = true;
        $get_curso = true;

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        $turnos = [
            0 => 'Selecione',
            1 => 'Matutino',
            2 => 'Vespertino',
            3 => 'Noturno',
            4 => 'Integral'
        ];

        $options = [
            'value'     => $this->turno,
            'resources' => $turnos,
            'required' => false
        ];

        $this->inputsHelper()->select('turno', $options);

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_serie_vaga = new clsPmieducarSerieVaga();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_serie_vaga->codUsuario = $this->pessoa_logada;
        }

        $obj_serie_vaga->setLimite($this->limite, $this->offset);

        $lista = $obj_serie_vaga->lista(
            $this->ano,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_serie
        );

        $total = $obj_serie_vaga->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
                $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
                $nm_serie = $det_ref_cod_serie['nm_serie'];

                $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $det_curso = $obj_curso->detalhe();
                $nm_curso = $det_curso['nm_curso'];

                $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola['nome'];

                // Dados para a url
                $url     = 'educar_serie_vaga_det.php';
                $options = ['query' => [
                    'cod_serie_vaga'  => $registro['cod_serie_vaga']
                ]];

                $this->addLinhas([
                    $urlHelper->l($registro['ano'], $url, $options),
                    $urlHelper->l($nm_escola, $url, $options),
                    $urlHelper->l($nm_curso, $url, $options),
                    $urlHelper->l($nm_serie, $url, $options),
                    $urlHelper->l($turnos[$registro['turno']], $url, $options),
                    $urlHelper->l($registro['vagas'], $url, $options)
                ]);
            }
        }

        $this->addPaginador2(
            'educar_serie_vaga_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(21253, $this->pessoa_logada, 7)) {
            $this->array_botao_url[] = 'educar_serie_vaga_cad.php';
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de vagas por série/ano', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Vagas por série';
        $this->processoAp = 21253;
    }
};
