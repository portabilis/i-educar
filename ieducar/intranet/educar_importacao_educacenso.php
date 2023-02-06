<?php

ini_set(option: 'max_execution_time', value: 0);

return new class extends clsCadastro {
    public $pessoa_logada;
    public $arquivo;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 9998849,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $this->breadcrumb(currentPage: 'Importação educacenso', breadcrumbs: [
            url('intranet/educar_educacenso_index.php') => 'Educacenso',
        ]);

        $this->titulo = 'Nova importação';

        return 'Editar';
    }

    public function Gerar()
    {
        $resources = [
            null => 'Selecione',
            '2019' => '2019',
            '2020' => '2020',
            '2021' => '2021',
            '2022' => '2022',
        ];

        $options = [
            'label' => 'Ano',
            'resources' => $resources,
            'value' => $this->ano,
        ];
        $this->inputsHelper()->select(attrName: 'ano', inputOptions: $options);

        $this->inputsHelper()->date(
            attrName: 'data_entrada_matricula',
            inputOptions: [
                'label' => 'Data de entrada das matrículas',
                'required' => true,
                'placeholder' => 'dd/mm/yyyy'
            ]
        );

        $this->campoArquivo(nome: 'arquivo', campo: 'Arquivo', valor: $this->arquivo, tamanho: 40, descricao: '<br/> <span style="font-style: italic; font-size= 10px;">* Somente arquivos com formato txt serão aceitos</span>');

        $this->nome_url_sucesso = 'Importar';

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: '/vendor/legacy/Educacenso/Assets/Javascripts/Importacao.js');
    }

    public function Novo(){}

    public function Editar(){}

    public function Formular()
    {
        $this->title = 'Importação educacenso';
        $this->processoAp = 9998849;
    }
};
