<?php

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
    public $cdd;
    public $volume;
    public $num_edicao;
    public $ano;
    public $num_paginas;
    public $isbn;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;
    public $ref_cod_assunto_acervo;
    public $ref_cod_acervo_autor;
    public $nm_autor;

    public function Gerar()
    {
        $this->titulo = 'Obras - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Obra',
            'Autor(es)',
            'CDD - Cutter',
            'ISBN'
        ];

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';

        $this->inputsHelper()->dynamic('ano', ['required' => false]);
        $this->inputsHelper()->dynamic('instituicao', ['required' => false]);
        $this->inputsHelper()->dynamic('escola', ['required' => false]);
        $this->inputsHelper()->dynamic('biblioteca', ['required' => false]);

        //retira escola e instituição do cabeçalho
        unset($lista_busca[5], $lista_busca[6]);

        $this->addCabecalhos($lista_busca);

        $opcoes_colecao = [];
        $opcoes_colecao[''] = 'Selecione';
        $opcoes_exemplar = [];
        $opcoes_exemplar[''] = 'Selecione';
        $opcoes_editora = [];
        $opcoes_editora[''] = 'Selecione';
        $opcoes_autor = [];
        $opcoes_autor[''] = 'Selecione';

        if (is_numeric($this->ref_cod_biblioteca)) {
            $obj_colecao = new clsPmieducarAcervoColecao();
            $obj_colecao->setOrderby('nm_colecao ASC');
            $obj_colecao->setCamposLista('cod_acervo_colecao, nm_colecao');
            $lst_colecao = $obj_colecao->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);
            if (is_array($opcoes)) {
                foreach ($lst_colecao as $colecao) {
                    $opcoes_colecao[$colecao['cod_acervo_colecao']] = $colecao['nm_colecao'];
                }
            }

            $obj_tp_exemplar = new clsPmieducarExemplarTipo();
            $obj_tp_exemplar->setCamposLista('cod_exemplar_tipo, nm_tipo');
            $obj_tp_exemplar->setOrderby('nm_tipo ASC');
            $lst_tp_exemplar = $obj_tp_exemplar->lista(null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1);
            if (is_array($lst_tp_exemplar)) {
                foreach ($lst_tp_exemplar as $tp_exemplar) {
                    $opcoes_exemplar[$tp_exemplar['cod_exemplar_tipo']] = $tp_exemplar['nm_tipo'];
                }
            }
            $obj_editora = new clsPmieducarAcervoEditora();
            $obj_editora->setCamposLista('cod_acervo_editora, nm_editora');
            $obj_editora->setOrderby('nm_editora ASC');
            $lst_editora = $obj_editora->lista(
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
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_biblioteca
            );
            if (is_array($lst_editora)) {
                foreach ($lst_editora as $editora) {
                    $opcoes_editora[$editora['cod_acervo_editora']] = $editora['nm_editora'];
                }
            }
        }

        $this->campoLista('ref_cod_acervo_colecao', 'Acervo Coleção', $opcoes_colecao, $this->ref_cod_acervo_colecao, '', false, '', '', false, false);
        $this->campoLista('ref_cod_exemplar_tipo', 'Tipo Exemplar', $opcoes_exemplar, $this->ref_cod_exemplar_tipo, '', false, '', '', false, false);
        $this->campoLista('ref_cod_acervo_editora', 'Editora', $opcoes_editora, $this->ref_cod_acervo_editora, '', false, '', '', false, false);

        $objTemp = new clsPmieducarAcervoAssunto();
        $lista = $objTemp->lista();

        $opcoes = [null => 'Selecione' ];

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['cod_acervo_assunto']] = $registro['nm_assunto'];
            }
        }

        $this->campoLista(
            'ref_cod_assunto_acervo',
            'Assunto',
            $opcoes,
            $this->ref_cod_assunto_acervo,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $this->campoTexto('titulo_livro', 'Titulo', $this->titulo_livro, 30, 255, false);
        $this->campoTexto('sub_titulo', 'Subtítulo', $this->sub_titulo, 30, 255, false);
        $this->campoTexto('cdd', 'CDD', $this->cdd, 30, 255, false);
        $this->campoTexto('cutter', 'Cutter', $this->cutter, 30, 255, false);
        $this->campoTexto('isbn', 'ISBN', $this->isbn, 30, 255, false);
        $this->campoTexto('nm_autor', 'Autor', $this->nm_autor, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if (!is_numeric($this->ref_cod_biblioteca)) {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_acervo = new clsPmieducarAcervo();
        $obj_acervo->setOrderby('titulo ASC');
        $obj_acervo->setLimite($this->limite, $this->offset);
        $obj_acervo->ref_cod_acervo_assunto = $this->ref_cod_assunto_acervo;

        $lista = $obj_acervo->listaAcervoBiblioteca($this->ref_cod_biblioteca, $this->titulo_livro, 1, $this->ref_cod_acervo_colecao, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo_editora, $this->sub_titulo, $this->cdd, $this->cutter, $this->isbn, $this->nm_autor);

        $total = $obj_acervo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_ref_cod_biblioteca['nm_biblioteca'];

                $lista_busca = [
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}\">{$registro['titulo']} {$registro['sub_titulo']}</a>",
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}\">{$registro['nm_autor']}</a>",
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}\">{$registro['cdd']} {$registro['cutter']}</a>",
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}\">{$registro['isbn']}</a>"
                ];

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
                    $lista_busca[] = "<a href=\"educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}\">{$registro['ref_cod_biblioteca']}</a>";
                } elseif ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4) {
                    $lista_busca[] = "<a href=\"educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}\">{$registro['ref_cod_biblioteca']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_acervo_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_acervo_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de obras', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-acervo-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Obras';
        $this->processoAp = '598';
    }
};
