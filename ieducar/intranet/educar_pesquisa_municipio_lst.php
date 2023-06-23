<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $idmun;

    public $nome;

    public $sigla_uf;

    public function Gerar()
    {
        Session::put(key: [
            'campo1' => $_GET['campo1'] ? $_GET['campo1'] : Session::get(key: 'campo1'),
        ]);
        Session::save();
        Session::start();

        $this->titulo = 'Municipio - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        //

        $this->addCabecalhos(coluna: [
            'Cidade',
            'Estado',
        ]);

        $array_uf = ['' => 'Todos'] + State::getListKeyAbbreviation()->toArray();

        if (!isset($this->sigla_uf)) {
            $this->sigla_uf = config(key: 'legacy.app.locale.province', default: '');
        }

        // outros Filtros

        $this->campoLista(nome: 'sigla_uf', campo: 'UF', valor: $array_uf, default: $this->sigla_uf, desabilitado: $disabled);
        $this->campoTexto(nome: 'nome', campo: 'Cidade', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $cities = City::query()
            ->with(relations: 'state')
            ->where(column: 'name', operator: 'ilike', value: "%{$this->nome}%")
            ->whereHas(relation: 'state', callback: function ($query) {
                $query->where('abbreviation', $this->sigla_uf);
            })
            ->orderBy(column: 'name')
            ->paginate(columns: ['*'], pageName: $pageName = "pagina_{$this->nome}");

        $total = $cities->total();

        foreach ($cities as $city) {
            $campo1 = Session::get(key: 'campo1');
            $script = " onclick=\"addSel1('{$campo1}','{$city->id}','{$city->name}'); fecha();\"";
            $this->addLinhas(linha: [
                "<a href=\"javascript:void(0);\" {$script}>{$city->name}</a>",
                "<a href=\"javascript:void(0);\" {$script}>{$city->state->name}</a>",
            ]);
        }

        $this->addPaginador2(strUrl: 'educar_pesquisa_municipio_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-pesquisa-municipio-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Municipio';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
