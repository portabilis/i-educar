<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
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

class indice extends clsListagem
{
    public $__limite;
    public $__offset;
    public $sigla_uf;
    public $nome;
    public $geom;
    public $idpais;

    public function Gerar()
    {
        $this->__titulo = 'Uf - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Sigla Uf',
            'Pais'
        ]);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['idpais']}"] = "{$registro['nome']}";
            }
        }

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', false, '', '', false, false);
        $this->campoTexto('sigla_uf', 'Sigla Uf', $this->sigla_uf, 3, 3, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 30, false);

        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        $obj_uf = new clsPublicUf();
        $obj_uf->setOrderby('nome ASC');
        $obj_uf->setLimite($this->__limite, $this->__offset);

        $lista = $obj_uf->lista(
            $this->nome,
            $this->geom,
            $this->idpais,
            $this->sigla_uf
        );

        $total = $obj_uf->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"public_uf_det.php?sigla_uf={$registro['sigla_uf']}\">{$registro['nome']}</a>",
                    "<a href=\"public_uf_det.php?sigla_uf={$registro['sigla_uf']}\">{$registro['sigla_uf']}</a>",
                    "<a href=\"public_uf_det.php?sigla_uf={$registro['sigla_uf']}\">{$registro['nm_pais']}</a>"
                ]);
            }
        }
        $this->addPaginador2('public_uf_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(754, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_uf_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de UFs', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
