<?php

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

    public $login;
    public $nm_cliente;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        foreach ($_GET as $campo => $valor) {
            $this->$campo = $valor;
        }
        Session::put([
            'campo1' => $_GET['campo1'] ?? Session::get('campo1'),
            'campo2' => $_GET['campo2'] ?? Session::get('campo2'),
        ]);
        Session::save();
        Session::start();

        $this->ref_cod_biblioteca = $this->ref_cod_biblioteca ? $this->ref_cod_biblioteca : $_GET['ref_cod_biblioteca'];

        $this->titulo = 'Cliente - Listagem';

        $this->addCabecalhos([
            'Código',
            'Cliente'
        ]);

        $this->campoTexto('nm_cliente', 'Cliente', $this->nm_cliente, 30, 255, false);
        $this->campoNumero('codigo', 'Código', $this->codigo, 15, 13);
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        if (isset($_GET['ref_cod_biblioteca'])) {
            $this->ref_cod_biblioteca = $_GET['ref_cod_biblioteca'];
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_acervo = new clsPmieducarCliente();
        $obj_acervo->setOrderby('nome ASC');
        $obj_acervo->setLimite($this->limite, $this->offset);

        if ($this->ref_cod_biblioteca) {
            $lista = $obj_acervo->listaPesquisaCliente(
                $this->codigo,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->nm_cliente,
                $this->ref_cod_biblioteca
            );
        } else {
            $lista = $obj_acervo->lista(
                $this->codigo,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->nm_cliente
            );
        }

        $total = $obj_acervo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $campo1 = Session::get('campo1');
                $campo2 = Session::get('campo2');
                if (is_string($campo1) && is_string($campo2)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_cliente']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_cliente']}'); fecha();\"";
                } elseif (is_string($campo1)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_cliente']}', '{$registro['nome']}'); fecha();\"";
                }
                $this->addLinhas([
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['cod_cliente']}</a>",
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['nome']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_pesquisa_cliente_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-cliente-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Cliente';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
