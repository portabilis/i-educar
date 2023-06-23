<?php

return new class extends clsCadastro
{
    const PROCESSO_AP = 9998900;

    public $ano;

    public $curso = [];

    public $data_inicial;

    public $data_final;

    public function Inicializar()
    {
        $this->ano = $this->getQueryString(name: 'ano');
        $this->curso = $this->getQueryString(name: 'curso');
        $this->data_inicial = $this->getQueryString(name: 'data_inicial');
        $this->data_final = $this->getQueryString(name: 'data_final');

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: self::PROCESSO_AP,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_index.php'
        );

        $this->nome_url_sucesso = 'Continuar';
        $this->url_cancelar = 'educar_index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Consulta de movimento geral', breadcrumbs: ['educar_index.php' => 'Escola']);

        return 'Novo';
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(helperNames: ['ano', 'instituicao']);
        $this->inputsHelper()->multipleSearchCurso(attrName: '', inputOptions: ['label' => 'Cursos', 'required' => false]);
        $this->inputsHelper()->dynamic(helperNames: ['dataInicial', 'dataFinal']);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: self::PROCESSO_AP,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'index.php'
        );

        $queryString = http_build_query(data: [
            'ano' => $this->ano,
            'curso' => $this->curso,
            'data_inicial' => $this->data_inicial,
            'data_final' => $this->data_final,
        ]);

        $url = '/intranet/educar_consulta_movimento_geral_lst.php?' . $queryString;

        $this->simpleRedirect(url: $url);
    }

    public function Formular()
    {
        $this->title = 'Consulta de movimento geral';
        $this->processoAp = 9998900;
    }
};
