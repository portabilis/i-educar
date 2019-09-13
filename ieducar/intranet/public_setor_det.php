<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicSetorBai.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Setor');
        $this->processoAp = 760;
    }
}

class indice extends clsDetalhe
{
    public $idsetorbai;
    public $nome;

    public function Gerar()
    {
        $this->titulo = 'Setor - Detalhe';

        $this->idsetorbai = $_GET['idsetorbai'];

        $tmp_obj = new clsPublicSetorBai($this->idsetorbai);
        $det_setor_bai = $tmp_obj->detalhe();

        if (!$det_setor_bai) {
            $this->simpleRedirect('public_setor_lst.php');
        } else {
            $registro = $det_setor_bai;
        }

        if ($registro['nome']) {
            $this->addDetalhe(['Setor', $registro['nome']]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(760, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_setor_cad.php';
            $this->url_editar = 'public_setor_cad.php?idsetorbai=' . $registro['idsetorbai'];
        }

        $this->url_cancelar = 'public_setor_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do setor', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
