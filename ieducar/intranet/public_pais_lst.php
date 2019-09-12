<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
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

class indice extends clsListagem
{
    public $__limite;
    public $__offset;
    public $idpais;
    public $nome;
    public $geom;

    public function Gerar()
    {
        $this->__titulo = 'Pais - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome'
        ]);

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, false);

        $this->__limite = 20;
        $this->__offset = isset($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        $obj_pais = new clsPublicPais();
        $obj_pais->setOrderby('nome ASC');
        $obj_pais->setLimite($this->__limite, $this->__offset);

        $lista = $obj_pais->lista(
            null,
            $this->nome
        );

        $total = $obj_pais->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"public_pais_det.php?idpais={$registro['idpais']}\">{$registro['nome']}</a>"
                ]);
            }
        }

        $this->addPaginador2('public_pais_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(753, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_pais_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de países', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
