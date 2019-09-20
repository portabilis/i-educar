<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Uf");
        $this->processoAp = 754;
    }
}

class indice extends clsDetalhe
{
    public $sigla_uf;
    public $nome;
    public $geom;
    public $idpais;

    public function Gerar()
    {
        $this->titulo = 'Uf - Detalhe';

        $this->sigla_uf = $_GET['sigla_uf'];

        $tmp_obj = new clsPublicUf($this->sigla_uf);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('public_uf_lst.php');
        }

        $obj_idpais = new clsPais($registro['idpais']);
        $det_idpais = $obj_idpais->detalhe();
        $registro['idpais'] = $det_idpais['nome'];

        if ($registro['sigla_uf']) {
            $this->addDetalhe(['Sigla Uf', "{$registro['sigla_uf']}"]);
        }
        if ($registro['nome']) {
            $this->addDetalhe(['Nome', "{$registro['nome']}"]);
        }
        if ($registro['geom']) {
            $this->addDetalhe(['Geom', "{$registro['geom']}"]);
        }
        if ($registro['idpais']) {
            $this->addDetalhe(['Pais', "{$registro['idpais']}"]);
        }
        if ($registro['cod_ibge']) {
            $this->addDetalhe(['Código INEP', "{$registro['cod_ibge']}"]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(754, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_uf_cad.php';
            $this->url_editar = "public_uf_cad.php?sigla_uf={$registro['sigla_uf']}";
        }

        $this->url_cancelar = 'public_uf_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da UF', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
