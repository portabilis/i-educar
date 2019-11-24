<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimento mensal');
        $this->processoAp = 9998910;
    }
}

class indice extends clsCadastro
{
    const PROCESSO_AP = 9998910;

    public $ano;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ref_cod_turma;

    public $data_inicial;

    public $data_final;

    public function Inicializar()
    {
        $this->ano = $this->getQueryString('ano');
        $this->ref_cod_instituicao = $this->getQueryString('ref_cod_instituicao');
        $this->ref_cod_escola = $this->getQueryString('ref_cod_escola');
        $this->ref_cod_curso = $this->getQueryString('ref_cod_curso');
        $this->ref_cod_serie = $this->getQueryString('ref_cod_serie');
        $this->ref_cod_turma = $this->getQueryString('ref_cod_turma');
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

        $this->breadcrumb('Consulta de movimento mensal', ['educar_index.php' => 'Escola']);

        return 'Novo';
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));
        $this->inputsHelper()->dynamic(array('curso', 'serie', 'turma'), array('required' => false));
        $this->inputsHelper()->dynamic(array('dataInicial', 'dataFinal'));
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

        $campos = [
            'ano',
            'ref_cod_instituicao',
            'ref_cod_escola',
            'ref_cod_curso',
            'ref_cod_serie',
            'ref_cod_turma',
            'data_inicial',
            'data_final',
        ];

        $queryString = [];

        foreach ($campos as $campo) {
            $queryString[$campo] = $this->{$campo};
        }

        $queryString = http_build_query($queryString);
        $url = 'educar_consulta_movimento_mensal_lst.php?' . $queryString;

        $this->simpleRedirect($url);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
