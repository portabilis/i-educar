<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
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
        Session::put([
            'campo1' => $_GET['campo1'] ? $_GET['campo1'] : Session::get('campo1')
        ]);
        Session::save();
        Session::start();

        $this->titulo = 'Municipio - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        //

        $this->addCabecalhos([
            'Cidade',
            'Estado'
        ]);

        $array_uf = ['' => 'Todos'] + State::getListKeyAbbreviation()->toArray();

        if (!isset($this->sigla_uf)) {
            $this->sigla_uf = config('legacy.app.locale.province', '');
        }

        // outros Filtros

        $this->campoLista('sigla_uf', 'UF', $array_uf, $this->sigla_uf, '', false, '', '', $disabled);
        $this->campoTexto('nome', 'Cidade', $this->nome, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $cities = City::query()
            ->with('state')
            ->where('name', 'ilike', "%{$this->nome}%")
            ->whereHas('state', function ($query) {
                $query->where('abbreviation', $this->sigla_uf);
            })
            ->orderBy('name')
            ->paginate(null, ['*'], $pageName = "pagina_{$this->nome}");

        $total = $cities->total();

        foreach ($cities as $city) {
            $campo1 = Session::get('campo1');
            $script = " onclick=\"addSel1('{$campo1}','{$city->id}','{$city->name}'); fecha();\"";
            $this->addLinhas([
                "<a href=\"javascript:void(0);\" {$script}>{$city->name}</a>",
                "<a href=\"javascript:void(0);\" {$script}>{$city->state->name}</a>"
            ]);
        }

        $this->addPaginador2('educar_pesquisa_municipio_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-municipio-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Municipio';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
