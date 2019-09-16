<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'App/Model/ZonaLocalizacao.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Bairro');
        $this->processoAp = 756;
    }
}

class indice extends clsDetalhe
{
    public $idmun;
    public $geom;
    public $idbai;
    public $nome;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;

    public function Gerar()
    {
        $this->titulo = 'Bairro - Detalhe';
        $this->idbai = $_GET['idbai'];

        $tmp_obj = new clsPublicBairro();
        $lst_bairro = $tmp_obj->lista(
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
            $this->idbai
        );

        if (!$lst_bairro) {
            $this->simpleRedirect('public_bairro_lst.php');
        } else {
            $registro = $lst_bairro[0];
        }

        if ($registro['nome']) {
            $this->addDetalhe(['Nome', $registro['nome']]);
        }

        $zona = App_Model_ZonaLocalizacao::getInstance();
        $zona = $zona->getValue($registro['zona_localizacao']);
        $this->addDetalhe(['Zona Localização', $zona]);

        if ($registro['nm_distrito']) {
            $this->addDetalhe(['Distrito', $registro['nm_distrito']]);
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

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(756, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_bairro_cad.php';
            $this->url_editar = 'public_bairro_cad.php?idbai=' . $registro['idbai'];
        }

        $this->url_cancelar = 'public_bairro_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do bairro', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
