<?php

use App\Models\Country;
use iEducar\Legacy\InteractWithDatabase;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';

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
    use InteractWithDatabase;

    public $__limite;
    public $__offset;
    public $idpais;
    public $nome;
    public $geom;

    public function model()
    {
        return Country::class;
    }

    public function index()
    {
        return 'public_pais_lst.php';
    }

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

        [$data, $total] = $this->paginate($this->__limite, $this->__offset, function ($query) {
            $query->when($this->nome, function ($query) {
                $query->whereUnaccent('name', $this->nome);
            });
            $query->orderBy('name');
        });

        foreach ($data as $item) {
            $this->addLinhas([
                "<a href=\"public_pais_det.php?idpais={$item->id}\">{$item->name}</a>"
            ]);
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
