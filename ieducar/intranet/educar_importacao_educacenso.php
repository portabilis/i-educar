<?php

ini_set('max_execution_time', 0);

return new class extends clsCadastro {
    public $pessoa_logada;

    public $arquivo;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            9998849,
            $this->pessoa_logada,
            7,
            'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $this->breadcrumb('Importação educacenso', [
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
        ];

        $options = [
            'label' => 'Ano',
            'resources' => $resources,
            'value' => $this->ano,
        ];
        $this->inputsHelper()->select('ano', $options);

        $this->inputsHelper()->date(
            'data_entrada_matricula',
            [
                'label' => 'Data de entrada das matrículas',
                'required' => true,
                'placeholder' => 'dd/mm/yyyy'
            ]
        );

        $this->campoArquivo('arquivo', 'Arquivo', $this->arquivo, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Somente arquivos com formato txt serão aceitos</span>');

        $this->nome_url_sucesso = 'Importar';

        Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Educacenso/Assets/Javascripts/Importacao.js');
    }

    public function Novo()
    {
        return;
    }

    public function Editar()
    {
        return;
    }

    public function Formular()
    {
        $this->title = 'Importação educacenso';
        $this->processoAp = 9998849;
    }
};
