<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicDistrito.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Distrito');
        $this->processoAp = 759;
    }
}

class indice extends clsDetalhe
{
    public $idmun;
    public $geom;
    public $iddis;
    public $nome;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;

    public function Gerar()
    {
        $this->titulo = 'Distrito - Detalhe';
        $this->iddis = $_GET['iddis'];

        $tmp_obj = new clsPublicDistrito();
        $lst_distrito = $tmp_obj->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->iddis
        );

        if (!$lst_distrito) {
            $this->simpleRedirect('public_distrito_lst.php');
        } else {
            $registro = $lst_distrito[0];
        }

        if ($registro['nome']) {
            $this->addDetalhe(['Nome', $registro['nome']]);
        }

        if ($registro['nm_municipio']) {
            $this->addDetalhe(['Município', $registro['nm_municipio']]);
        }

        if ($registro['nm_estado']) {
            $this->addDetalhe(['Estado', $registro['nm_estado']]);
        }

        if ($registro['nm_pais']) {
            $this->addDetalhe(['Pais', $registro['nm_pais']]);
        }

        if ($registro['cod_ibge']) {
            $this->addDetalhe(['Código INEP', $registro['cod_ibge']]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(759, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_distrito_cad.php';
            $this->url_editar = 'public_distrito_cad.php?iddis=' . $registro['iddis'];
        }

        $this->url_cancelar = 'public_distrito_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do distrito', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
