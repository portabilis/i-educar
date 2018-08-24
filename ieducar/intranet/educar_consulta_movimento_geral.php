<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimento geral');
        $this->addEstilo("localizacaoSistema");
        $this->processoAp = 9998900;
    }
}

class indice extends clsCadastro
{
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
            9998900,
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

    function Novo()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        $campos = ['ano', 'curso', 'data_inicial', 'data_final'];
        $queryString = [];

        foreach ($campos as $campo) {
            $queryString[$campo] = $this->{$campo};
        }

        $queryString = http_build_query($queryString);
        $url = 'educar_consulta_movimento_geral_lst.php?' . $queryString;

        header('Location: ' . $url);
        die();
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
