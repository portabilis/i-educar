<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Município");
        $this->processoAp = 755;
    }
}

class indice extends clsDetalhe
{
    public $idmun;
    public $nome;
    public $sigla_uf;
    public $cod_ibge;
    public $geom;
    public $tipo;
    public $idpes_rev;
    public $idpes_cad;
    public $data_rev;
    public $data_cad;
    public $origem_gravacao;
    public $operacao;

    public function Gerar()
    {
        $this->titulo = 'Município - Detalhe';

        $this->idmun = $_GET['idmun'];

        $tmp_obj = new clsPublicMunicipio($this->idmun);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('public_municipio_lst.php');
        }

        $obj_uf = new clsUf($registro['sigla_uf']);
        $det_uf = $obj_uf->detalhe();

        $obj_pais = new clsPais($det_uf['idpais']->idpais);
        $det_pais = $obj_pais->detalhe();
        $registro['idpais'] = $det_pais['nome'];

        $obj_sigla_uf = new clsUf($registro['sigla_uf']);
        $det_sigla_uf = $obj_sigla_uf->detalhe();
        $registro['sigla_uf'] = $det_sigla_uf['nome'];

        $obj_idmun_pai = new clsMunicipio($registro['idmun_pai']);
        $det_idmun_pai = $obj_idmun_pai->detalhe();
        $registro['idmun_pai'] = $det_idmun_pai['nome'];

        if ($registro['nome']) {
            $this->addDetalhe(['Nome', "{$registro['nome']}"]);
        }
        if ($registro['sigla_uf']) {
            $this->addDetalhe(['Estado', "{$registro['sigla_uf']}"]);
        }
        if ($registro['idpais']) {
            $this->addDetalhe(['Pais', "{$registro['idpais']}"]);
        }
        if ($registro['area_km2']) {
            $this->addDetalhe(['Area Km2', "{$registro['area_km2']}"]);
        }
        if ($registro['tipo']) {
            $this->addDetalhe(['Tipo', "{$registro['tipo']}"]);
        }
        if ($registro['idmun_pai']) {
            $this->addDetalhe(['Idmun Pai', "{$registro['idmun_pai']}"]);
        }
        if ($registro['origem_gravacao']) {
            $this->addDetalhe(['Origem Gravação', "{$registro['origem_gravacao']}"]);
        }
        if ($registro['operacao']) {
            $this->addDetalhe(['Operacão', "{$registro['operacao']}"]);
        }
        if ($registro['cod_ibge']) {
            $this->addDetalhe(['Código INEP', "{$registro['cod_ibge']}"]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(755, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_municipio_cad.php';
            $this->url_editar = "public_municipio_cad.php?idmun={$registro['idmun']}";
        }

        $this->url_cancelar = 'public_municipio_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do município', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
