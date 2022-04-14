<?php

return new class extends clsCadastro {
    const PROCESSO_AP = 9998900;

    public $ano;

    public $curso = [];

    public $data_inicial;

    public $data_final;

    public function Inicializar()
    {
        $this->ano = $this->getQueryString('ano');
        $this->curso = $this->getQueryString('curso');
        $this->data_inicial = $this->getQueryString('data_inicial');
        $this->data_final = $this->getQueryString('data_final');

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            self::PROCESSO_AP,
            $this->pessoa_logada,
            7,
            'educar_index.php'
        );

        $this->nome_url_sucesso = 'Continuar';
        $this->url_cancelar = 'educar_index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Consulta de movimento geral', ['educar_index.php' => 'Escola']);

        return 'Novo';
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(['ano', 'instituicao']);
        $this->inputsHelper()->multipleSearchCurso('', ['label' => 'Cursos','required' => false]);
        $this->inputsHelper()->dynamic(['dataInicial', 'dataFinal']);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            self::PROCESSO_AP,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        $queryString = http_build_query([
            'ano' => $this->ano,
            'curso' => $this->curso,
            'data_inicial' => $this->data_inicial,
            'data_final' => $this->data_final,
        ]);

        $url = '/intranet/educar_consulta_movimento_geral_lst.php?' . $queryString;

        $this->simpleRedirect($url);
    }

    public function Formular()
    {
        $this->title = 'Consulta de movimento geral';
        $this->processoAp = 9998900;
    }
};
