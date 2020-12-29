<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'Educacenso/Model/DocenteDataMapper.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Servidores - Servidor alocação');
        $this->processoAp = 635;
    }
}

class indice extends clsDetalhe
{
    public $titulo;

    public $cod_servidor_alocacao = null;
    public $ref_cod_servidor = null;
    public $ref_cod_instituicao = null;
    public $ref_cod_servidor_funcao = null;
    public $ref_cod_funcionario_vinculo = null;
    public $ano = null;
    public $data_admissao = null;
    public $data_saida = null;

    public function Gerar()
    {
        $this->titulo = 'Servidor alocação - Detalhe';

        $this->cod_servidor_alocacao = $_GET['cod_servidor_alocacao'];

        $tmp_obj = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, $this->ref_cod_instituicao);

        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $this->ref_cod_servidor            = $registro['ref_cod_servidor'];
        $this->ref_cod_instituicao         = $registro['ref_ref_cod_instituicao'];
        $this->ref_cod_servidor_funcao     = $registro['ref_cod_servidor_funcao'];
        $this->data_admissao               = $registro['data_admissao'];
        $this->data_saida                  = $registro['data_saida'];
        $this->ref_cod_funcionario_vinculo = $registro['ref_cod_funcionario_vinculo'];
        $this->ano                         = $registro['ano'];

        //Nome do servidor
        $fisica = new clsPessoaFisica($this->ref_cod_servidor);
        $fisica = $fisica->detalhe();

        $this->addDetalhe(['Servidor', "{$fisica['nome']}"]);

        //Escola
        $escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $escola = $escola->detalhe();

        $this->addDetalhe(['Escola', "{$escola['nome']}"]);

        //Ano
        $this->addDetalhe(['Ano', "{$registro['ano']}"]);

        //Periodo
        $periodo = [
            1  => 'Matutino',
            2  => 'Vespertino',
            3  => 'Noturno'
        ];

        $this->addDetalhe(['Periodo', "{$periodo[$registro['periodo']]}"]);

        //Carga horária
        $this->addDetalhe(['Carga horária', substr($registro['carga_horaria'], 0, - 3)]);

        //Função
        if ($this->ref_cod_servidor_funcao) {
            $funcaoServidor = new clsPmieducarServidorFuncao(null, null, null, null, $this->ref_cod_servidor_funcao);
            $funcaoServidor = $funcaoServidor->detalhe();

            $funcao = new clsPmieducarFuncao($funcaoServidor['ref_cod_funcao']);
            $funcao = $funcao->detalhe();

            $this->addDetalhe(['Função', "{$funcao['nm_funcao']}"]);
        }

        //Vinculo
        if ($this->ref_cod_funcionario_vinculo) {
            $funcionarioVinculo = new clsPortalFuncionario();
            $funcionarioVinculo = $funcionarioVinculo->getNomeVinculo($registro['ref_cod_funcionario_vinculo']);

            $this->addDetalhe(['Vinculo', "{$funcionarioVinculo}"]);
        }

        if (!empty($this->data_admissao)) {
            $this->addDetalhe(['Data de admissão', Portabilis_Date_Utils::pgSQLToBr($this->data_admissao)]);
        }

        if (!empty($this->data_saida)) {
            $this->addDetalhe(['Data de saída', Portabilis_Date_Utils::pgSQLToBr($this->data_saida)]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->url_novo   = "educar_servidor_alocacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
            $this->url_editar = "educar_servidor_alocacao_cad.php?cod_servidor_alocacao={$this->cod_servidor_alocacao}";
        }

        $this->url_cancelar = "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da alocação', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
    }
}

// Instancia o objeto da página
$pagina = new clsIndexBase();

// Instancia o objeto de conteúdo
$miolo = new indice();

// Passa o conteúdo para a página
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
