<?php

use App\Models\District;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

return new class extends clsListagem {
    use InteractWithDatabase, SelectOptions;

    public $__limite;
    public $__offset;
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
    public $idpais;
    public $iduf;

    public function model()
    {
        return District::class;
    }

    public function index()
    {
        return 'public_distrito_lst.php';
    }

    public function Gerar()
    {
        $this->__titulo = 'Distrito - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Município',
            'Estado',
            'Pais'
        ]);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista(
            'idpais',
            'Pais',
            $opcoes,
            $this->idpais,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista(
            'iduf',
            'Estado',
            $opcoes,
            $this->iduf,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $opcoes = ['' => 'Selecione'];

        if ($this->iduf) {
            $opcoes += $this->getCities($this->iduf);
        }

        $this->campoLista(
            'idmun',
            'Município',
            $opcoes,
            $this->idmun,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, false);

        $this->__limite = 20;
        $this->__offset = ($_GET['pagina_' . $this->nome]) ? ($_GET['pagina_' . $this->nome] * $this->__limite - $this->__limite) : 0;

        [$data, $total] = $this->paginate($this->__limite, $this->__offset, function ($query) {
            $query->with('city.state.country');
            $query->orderBy('name');
            $query->when($this->nome, function ($query) {
                $query->whereUnaccent('name', $this->nome);
            });
            $query->when($this->idmun, function ($query) {
                $query->where('city_id', $this->idmun);
            });
        });

        $url = CoreExt_View_Helper_UrlHelper::getInstance();
        $options = ['query' => ['iddis' => null]];

        foreach ($data as $district) {
            $options['query']['iddis'] = $district->id;

            $this->addLinhas([
                $url->l($district->name, 'public_distrito_det.php', $options),
                $url->l($district->city->name, 'public_distrito_det.php', $options),
                $url->l($district->city->state->name, 'public_distrito_det.php', $options),
                $url->l($district->city->state->country->name, 'public_distrito_det.php', $options)
            ]);
        }

        $this->addPaginador2('public_distrito_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(759, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_distrito_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de distritos', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__  . '/scripts/extra/public-distrito-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Distrito';
        $this->processoAp = 759;
    }
};
