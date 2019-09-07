<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicSetorBai.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Setor');
        $this->processoAp = 760;
    }
}

class indice extends clsListagem
{
    public $__limite;
    public $__offset;
    public $idsetorbai;
    public $nome;

    public function Gerar()
    {
        $this->__titulo = 'Setor - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Código',
            'Nome'
        ]);

        $this->campoNumero('idsetorbai', 'Código', $this->idsetorbai, 5, 5, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, false);

        $this->__limite = 20;
        $this->__offset = ($_GET['pagina_' . $this->nome]) ? ($_GET['pagina_' . $this->nome] * $this->__limite - $this->__limite) : 0;

        $obj_setor = new clsPublicSetorBai();
        $obj_setor->setOrderby('nome ASC');
        $obj_setor->setLimite($this->__limite, $this->__offset);

        $lista = $obj_setor->lista(
            $this->idsetorbai,
            $this->nome
        );

        $total = $obj_setor->_total;

        $url = CoreExt_View_Helper_UrlHelper::getInstance();
        $options = ['query' => ['idsetorbai' => null]];

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $options['query']['idsetorbai'] = $registro['idsetorbai'];
                $this->addLinhas([
                    $url->l($registro['idsetorbai'], 'public_setor_det.php', $options),
                    $url->l($registro['nome'], 'public_setor_det.php', $options),
                ]);
            }
        }

        $this->addPaginador2('public_setor_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(760, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_setor_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de setores', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
