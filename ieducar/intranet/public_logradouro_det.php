<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/urbano/clsUrbanoTipoLogradouro.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Logradouro");
        $this->processoAp = 757;
    }
}

class indice extends clsDetalhe
{
    public $idlog;
    public $idtlog;
    public $nome;
    public $idmun;
    public $geom;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $idpais;
    public $sigla_uf;

    public function Gerar()
    {
        $this->titulo = 'Logradouro - Detalhe';

        $this->idlog = $_GET['idlog'];

        $obj_logradouro = new clsPublicLogradouro();
        $lst_logradouro = $obj_logradouro->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idlog);

        if (!$lst_logradouro) {
            $this->simpleRedirect('public_logradouro_lst.php');
        } else {
            $registro = $lst_logradouro[0];
        }

        $obj_idtlog = new clsUrbanoTipoLogradouro($registro['idtlog']);
        $det_idtlog = $obj_idtlog->detalhe();
        $registro['idtlog'] = $det_idtlog['descricao'];

        if ($registro['idlog']) {
            $this->addDetalhe(['Código', "{$registro['idlog']}"]);
        }
        if ($registro['idtlog']) {
            $this->addDetalhe(['Tipo', "{$registro['idtlog']}"]);
        }
        if ($registro['nome']) {
            $this->addDetalhe(['Nome', "{$registro['nome']}"]);
        }
        if ($registro['nm_municipio']) {
            $this->addDetalhe(['Município', "{$registro['nm_municipio']}"]);
        }
        if ($registro['nm_estado']) {
            $this->addDetalhe(['Estado', "{$registro['nm_estado']}"]);
        }
        if ($registro['nm_pais']) {
            $this->addDetalhe(['País', "{$registro['nm_pais']}"]);
        }
        if ($registro['ident_oficial']) {
            $this->addDetalhe(['Ident Oficial', "{$registro['ident_oficial']}"]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(757, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_logradouro_cad.php';
            $this->url_editar = "public_logradouro_cad.php?idlog={$registro['idlog']}";
        }

        $this->url_cancelar = 'public_logradouro_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do logradouro', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
