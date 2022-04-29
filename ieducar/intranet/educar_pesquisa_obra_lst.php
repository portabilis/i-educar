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

    public $ref_cod_biblioteca;
    public $ref_cod_exemplar;
    public $nm_obra;
    public $titulo_obra;
    public $ref_cod_acervo;
    public $ref_acervo_autor;
    public $isbn;

    public function Gerar()
    {
        foreach ($_GET as $campo => $valor) {
            $this->$campo = $valor;
        }

        Session::put([
            'campo1' => $_GET['campo1'] ? $_GET['campo1'] : Session::get('campo1'),
            'campo2' => $_GET['campo2'] ? $_GET['campo2'] : Session::get('campo2'),
            'campo3' => $_GET['campo3'] ? $_GET['campo3'] : Session::get('campo3'),
        ]);
        Session::save();
        Session::start();

        $this->titulo = 'Obra - Listagem';

        $this->addCabecalhos([
            'Obra',
            'Autor',
            'ISBN'
        ]);

        $this->campoTexto('titulo_obra', 'Obra', $this->nm_obra, 30, 255, false);
        $this->campoTexto('ref_acervo_autor', 'Autor', $this->ref_acervo_autor, 30, 255, false);
        $this->campoNumero('isbn', 'ISBN', $this->isbn, 15, 15, false);
        $this->ref_cod_biblioteca = Session::get('campo3');

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_acervo = new clsPmieducarAcervo();
        $obj_acervo->setOrderby('titulo ASC');
        $obj_acervo->setLimite($this->limite, $this->offset);

        $lista = $obj_acervo->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->titulo_obra,
            null,
            null,
            null,
            null,
            $this->isbn ? $this->isbn : null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_biblioteca,
            null,
            null,
            $this->ref_acervo_autor
        );

        $total = $obj_acervo->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_acervo_autor = new clsPmieducarAcervoAutor($registro['cod_acervo_autor']);
                $det_acervo_autor = $obj_acervo_autor->detalhe();
                $registro['cod_acervo_autor'] = $det_acervo_autor['nm_autor'];
                $campo1 = Session::get('campo1');
                $campo2 = Session::get('campo2');
                $script = " onclick=\"addVal1('{$campo1}',{$registro['cod_acervo']}); addVal1('{$campo2}','{$registro['titulo']}'); addVal1('cod_biblioteca','{$this->ref_cod_biblioteca}'); fecha();\"";
                $tituloSubtitulo = $registro['titulo'] . ' ' . $registro['sub_titulo'];
                $this->addLinhas([
                    "<a href=\"javascript:void(0);\" {$script}>{$tituloSubtitulo}</a>",
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['cod_acervo_autor']}</a>",
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['isbn']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_pesquisa_obra_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-obra-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Obra';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
