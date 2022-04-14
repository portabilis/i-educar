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

    public $cod_acervo;
    public $ref_cod_exemplar_tipo;
    public $ref_cod_acervo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_acervo_colecao;
    public $ref_cod_acervo_idioma;
    public $ref_cod_acervo_editora;
    public $titulo_livro;
    public $sub_titulo;
    public $cdu;
    public $cutter;
    public $volume;
    public $num_edicao;
    public $ano;
    public $num_paginas;
    public $isbn;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        $this->ref_cod_biblioteca = $_GET['ref_cod_biblioteca'] ? $_GET['ref_cod_biblioteca'] : Session::get('ref_cod_biblioteca');
        Session::put('ref_cod_biblioteca', $this->ref_cod_biblioteca);
        Session::put('campo1', $_GET['campo1'] ?? Session::get('campo1'));
        Session::save();
        Session::start();

        foreach ($_GET as $key => $value) {
            $this->$key = $value;
        }
        $this->titulo = 'Obras - Listagem';

        //

        $this->addCabecalhos([
            'Obra',
            'Biblioteca'
        ]);

        // outros Filtros
        //$get_escola     = 1;
        //$get_biblioteca = 1;
        //$obrigatorio    = false;
        //include("include/pmieducar/educar_campo_lista.php");
        $this->campoTexto('titulo_livro', 'Titulo', $this->titulo_livro, 30, 255, false);
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

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
            $this->titulo_livro,
            null,
            null,
            null,
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
            $this->ref_cod_biblioteca,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola
        );

        $total = $obj_acervo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $obj_det = $obj_biblioteca->detalhe();

                $registro['ref_cod_biblioteca'] = $obj_det['nm_biblioteca'];

                $campo1 = Session::get('campo1');
                $script = " onclick=\"addSel1('{$campo1}','{$registro['cod_acervo']}','{$registro['titulo']}'); fecha();\"";
                $this->addLinhas([
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['titulo']}</a>",
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['ref_cod_biblioteca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_pesquisa_acervo_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-acervo-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Obras';
        $this->processoAp = '598';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
