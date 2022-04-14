<?php

use App\Models\State;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;
use Illuminate\Support\Str;

return new class extends clsListagem {
    use InteractWithDatabase, SelectOptions;

    public $__limite;
    public $__offset;
    public $sigla_uf;
    public $nome;
    public $idpais;

    public function model()
    {
        return State::class;
    }

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

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', false, '', '', false, false);
        $this->campoTexto('sigla_uf', 'Sigla Uf', $this->sigla_uf, 3, 3, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 30, false);

        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        [$data, $total] = $this->paginate($this->__limite, $this->__offset, function ($query) {
            $query->with('country');
            $query->orderBy('name');
            $query->when($this->nome, function ($query) {
                $query->whereUnaccent('name', $this->nome);
            });
            $query->when($this->idpais, function ($query) {
                $query->where('country_id', $this->idpais);
            });
            $query->when($this->sigla_uf, function ($query) {
                $query->where('abbreviation', Str::upper($this->sigla_uf));
            });
        });

        foreach ($data as $item) {
            $this->addLinhas([
                "<a href=\"public_uf_det.php?id={$item->id}\">{$item->name}</a>",
                "<a href=\"public_uf_det.php?id={$item->id}\">{$item->abbreviation}</a>",
                "<a href=\"public_uf_det.php?id={$item->id}\">{$item->country->name}</a>"
            ]);
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

    public function Formular()
    {
        $this->title = 'Uf';
        $this->processoAp = 754;
    }
};
