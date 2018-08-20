<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimentação geral');
        $this->addEstilo("localizacaoSistema");
        $this->processoAp = 561; // TODO: mudar para o id real do menu
    }
}

class indice extends clsCadastro
{
    public $pessoa_logada;

    public $ano;

    public $curso = [];

    public $data_inicial;

    public $data_final;

    public function __construct()
    {
        parent::__construct();

        $this->pessoa_logada = $this->getSession()->id_pessoa ?? null;
    }

    public function Inicializar()
    {
        $this->ano = $_GET['ano'] ?? null;
        $this->curso = $_GET['curso'] ?? null;
        $this->data_inicial = $_GET['data_inicial'] ?? null;
        $this->data_final = $_GET['data_final'] ?? null;

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        $this->nome_url_sucesso = 'Continuar';
        $this->url_cancelar = 'index.php';

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            '' => 'Consulta de movimentação geral'
        ]);

        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = 'Cancelar';

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
