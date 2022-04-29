<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $cod_serie;
    public $ref_cod_curso;
    public $nm_serie;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Componentes da série';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
      'Série',
      'Curso'
    ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(['instituicao', 'curso', 'serie'], ['required' => false]);

        // Paginador
        $this->limite = 10;
        $this->offset = $_GET["pagina_{$this->nome}"] ?
      $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;

        $obj_serie = new clsPmieducarSerie();
        $obj_serie->setOrderby('nm_serie ASC');
        $obj_serie->setLimite($this->limite, $this->offset);

        $lista = $obj_serie->listaSeriesComComponentesVinculados(
            $this->ref_cod_serie,
            $this->ref_cod_curso,
            $this->ref_cod_instituicao,
            1
        );

        $total = $obj_serie->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {

        // Pega detalhes de foreign_keys
                $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
                $registro['ref_cod_curso'] = empty($det_ref_cod_curso['descricao']) ? $det_ref_cod_curso['nm_curso'] : "{$det_ref_cod_curso['nm_curso']} ({$det_ref_cod_curso['descricao']})";
                $registro['nm_serie'] = empty($registro['descricao']) ? $registro['nm_serie'] : "{$registro['nm_serie']} ({$registro['descricao']})";
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
          "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro['cod_serie']}\">{$registro['nm_serie']}</a>",
          "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro['cod_serie']}\">{$registro['ref_cod_curso']}</a>"
        ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro['cod_serie']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_componentes_serie_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(9998859, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_componentes_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Componentes da série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        $scripts = ['/modules/Cadastro/Assets/Javascripts/ComponentesSerieFiltros.js'];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Formular()
    {
        $this->title = 'Componentes da série';
        $this->processoAp = '9998859';
    }
};
