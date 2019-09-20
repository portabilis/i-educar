<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Pais");
        $this->processoAp = 753;
    }
}

class indice extends clsDetalhe
{
    public $idpais;
    public $nome;
    public $geom;

    public function Gerar()
    {
        $this->titulo = 'Pais - Detalhe';

        $this->idpais = $_GET['idpais'];

        $tmp_obj = new clsPublicPais($this->idpais);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('public_pais_lst.php');
        }

        if ($registro['nome']) {
            $this->addDetalhe(['Nome', "{$registro['nome']}"]);
        }
        if ($registro['geom']) {
            $this->addDetalhe(['Geom', "{$registro['geom']}"]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(753, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_pais_cad.php';
            $this->url_editar = "public_pais_cad.php?idpais={$registro['idpais']}";
        }

        $this->url_cancelar = 'public_pais_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do país', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
