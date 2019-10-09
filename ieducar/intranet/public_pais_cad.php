<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Pais");
        $this->processoAp = '753';
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $idpais;
    public $nome;
    public $geom;
    public $cod_ibge;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->idpais=$_GET['idpais'];

        if (is_numeric($this->idpais)) {
            $obj = new clsPublicPais($this->idpais);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

//              $this->fexcluir = true;

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "public_pais_det.php?idpais={$registro['idpais']}" : 'public_pais_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
         'educar_enderecamento_index.php'    => 'Endereçamento',
         ''        => "{$nomeMenu} país"
    ]);
        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('idpais', $this->idpais);

        // text
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, true);

        $this->inputsHelper()->integer(
            'cod_ibge',
            [
                'label' => 'Código INEP',
                'required' => false,
                'label_hint' => 'Somente números',
                'max_length' => 12,
                'placeholder' => 'INEP'
            ]
        );

        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge, 30, 8, true);
    }

    public function Novo()
    {
        $obj = new clsPublicPais(null, $this->nome, $this->geom, $this->cod_ibge);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $enderecamento = new clsPublicPais($cadastrou);
            $enderecamento->cadastrou = $cadastrou;
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de País', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('public_pais_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $enderecamentoDetalhe = new clsPublicPais($this->idpais);
        $enderecamentoDetalhe->cadastrou = $this->idpais;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicPais($this->idpais, $this->nome, $this->geom, $this->cod_ibge);
        $editou = $obj->edita();

        if ($editou) {
            $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de País', $this->pessoa_logada, $this->idpais);
            $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('public_pais_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPublicPais($this->idpais);

        $enderecamento = $obj->detalhe();
        $enderecamentoDetalhe->cadastrou = $this->cadastrou;

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de País', $this->pessoa_logada, $this->cadastrou);
            $auditoria->exclusao($enderecamento);

            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('public_pais_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
