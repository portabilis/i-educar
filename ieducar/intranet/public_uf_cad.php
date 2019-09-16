<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'App/Model/Pais.php';
require_once 'App/Model/NivelAcesso.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Uf");
        $this->processoAp = 754;
    }
}

class indice extends clsCadastro
{
    public $sigla_uf;
    public $nome;
    public $geom;
    public $idpais;
    public $cod_ibge;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->sigla_uf = $_GET['sigla_uf'];

        if (is_string($this->sigla_uf)) {
            $obj = new clsPublicUf($this->sigla_uf);
            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = $retorno == 'Editar' ? "public_uf_det.php?sigla_uf={$registro['sigla_uf']}" : 'public_uf_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $this->breadcrumb("{$nomeMenu} UF", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['idpais']}"] = "{$registro['nome']}";
            }
        }

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);
        $this->campoTexto('sigla_uf', 'Sigla Uf', $this->sigla_uf, 3, 3, true);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 30, true);
        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge);

        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/cad_uf.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de UFs brasileiras, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $obj = new clsPublicUf(strtoupper($this->sigla_uf));
        $duplica = $obj->verificaDuplicidade();

        if ($duplica) {
            $this->mensagem = 'A sigla já existe para outro estado.<br>';

            return false;
        } else {
            $obj = new clsPublicUf($this->sigla_uf, $this->nome, $this->geom, $this->idpais, $this->cod_ibge);
            $cadastrou = $obj->cadastra();
            if ($cadastrou) {
                $enderecamento = new clsPublicUf($cadastrou);
                $enderecamento->cadastrou = $cadastrou;
                $enderecamento = $enderecamento->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Estado', $this->pessoa_logada, $cadastrou);
                $auditoria->inclusao($enderecamento);

                $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect('public_uf_lst.php');
            }

            $this->mensagem = 'Cadastro não realizado.<br>';

            return false;
        }
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de UFs brasileiras, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $enderecamentoDetalhe = new clsPublicUf($this->sigla_uf);
        $enderecamentoDetalhe->cadastrou = $this->sigla_uf;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicUf(strtoupper($this->sigla_uf));
        $duplica = $obj->verificaDuplicidade();

        if ($duplica) {
            $this->mensagem = 'A sigla já existe para outro estado.<br>';

            return false;
        } else {
            $obj = new clsPublicUf($this->sigla_uf, $this->nome, $this->geom, $this->idpais, $this->cod_ibge);
            $editou = $obj->edita();

            if ($editou) {
                $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Estado', $this->pessoa_logada, $this->sigla_uf);
                $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

                $this->mensagem = 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect('public_uf_lst.php');
            }

            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de UFs brasileiras, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $obj = new clsPublicUf($this->sigla_uf);
        $enderecamento = $obj->detalhe();
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $auditoria = new clsPublicUf('Endereçamento de Estado', $this->pessoa_logada, $this->sigla_uf);
            $auditoria->exclusao($enderecamento);

            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('public_uf_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
