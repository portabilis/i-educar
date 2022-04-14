<?php

use App\Models\City;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

return new class extends clsListagem {
    use InteractWithDatabase, SelectOptions;

    public $__limite;
    public $__offset;
    public $idmun;
    public $nome;
    public $sigla_uf;
    public $cod_ibge;
    public $geom;
    public $tipo;
    public $idpes_rev;
    public $idpes_cad;
    public $data_rev;
    public $data_cad;
    public $origem_gravacao;
    public $operacao;
    public $idpais;

    public function model()
    {
        return City::class;
    }

    public function Gerar()
    {
        $this->__titulo = 'Município - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Estado'
        ]);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', false, '', '', false, false);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista('iduf', 'Estado', $opcoes, $this->iduf, '', false, '', '', false, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, false);

        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        [$data, $total] = $this->paginate($this->__limite, $this->__offset, function ($query) {
            $query->with('state');
            $query->orderBy('name');
            $query->when($this->nome, function ($query) {
                $query->whereUnaccent('name', $this->nome);
            });
            $query->when($this->iduf, function ($query) {
                $query->where('state_id', $this->iduf);
            });
        });

        foreach ($data as $item) {
            $this->addLinhas([
                "<a href=\"public_municipio_det.php?idmun={$item->id}\">{$item->name}</a>",
                "<a href=\"public_municipio_det.php?idmun={$item->id}\">{$item->state->abbreviation}</a>"
            ]);
        }

        $this->addPaginador2('public_municipio_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(755, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_municipio_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de municípios', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/public-municipio-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Município';
        $this->processoAp = 755;
    }
};
