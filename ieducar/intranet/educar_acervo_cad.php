<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

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
    public $dimencao;
    public $ref_cod_tipo_autor;
    public $tipo_autor;
    public $material_ilustrativo;
    public $dimencao_ilustrativo;
    public $local;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $checked;

    public $acervo_autor;
    public $ref_cod_acervo_autor;
    public $principal;
    public $incluir_autor;
    public $excluir_autor;

    public $colecao;
    public $editora;
    public $idioma;
    public $autor;

    protected function setSelectionFields()
    {
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_acervo=$_GET['cod_acervo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_acervo_lst.php');

        if (is_numeric($this->cod_acervo)) {
            $obj = new clsPmieducarAcervo($this->cod_acervo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                $obj_det = $obj_biblioteca->detalhe();

                $this->ref_cod_instituicao = $obj_det['ref_cod_instituicao'];
                $this->ref_cod_escola = $obj_det['ref_cod_escola'];

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(598, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_acervo_det.php?cod_acervo={$registro['cod_acervo']}" : 'educar_acervo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' obra', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }
        if (is_numeric($this->colecao)) {
            $this->ref_cod_acervo_colecao = $this->colecao;
        }
        if (is_numeric($this->editora)) {
            $this->ref_cod_acervo_editora = $this->editora;
        }
        if (is_numeric($this->idioma)) {
            $this->ref_cod_acervo_idioma = $this->idioma;
        }
        if (is_numeric($this->autor)) {
            $this->ref_cod_acervo_autor = $this->autor;
        }

        // primary keys
        $this->campoOculto('cod_acervo', $this->cod_acervo);
        $this->campoOculto('colecao', '');
        $this->campoOculto('editora', '');
        $this->campoOculto('idioma', '');
        $this->campoOculto('autor', '');

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca', 'bibliotecaTipoExemplar']);

        // Obra referência
        $opcoes = [ 'NULL' => 'Selecione' ];

        if ($this->ref_cod_acervo && $this->ref_cod_acervo != 'NULL') {
            $objTemp = new clsPmieducarAcervo($this->ref_cod_acervo);
            $detalhe = $objTemp->detalhe();
            if ($detalhe) {
                $opcoes["{$detalhe['cod_acervo']}"] = "{$detalhe['titulo']}";
            }
        }

        $this->campoLista('ref_cod_acervo', 'Obra Refer&ecirc;ncia', $opcoes, $this->ref_cod_acervo, '', false, '', "<img border=\"0\" class=\"btn\" onclick=\"pesquisa();\" id=\"ref_cod_acervo_lupa\" name=\"ref_cod_acervo_lupa\" src=\"imagens/lupaT.png\"\/>", false, false);

        // Coleção
        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarAcervoColecao();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_acervo_colecao']}"] = "{$registro['nm_colecao']}";
            }
        }
        $this->campoLista('ref_cod_acervo_colecao', 'Cole&ccedil;&atilde;o', $opcoes, $this->ref_cod_acervo_colecao, '', false, '', '<img id=\'img_colecao\' src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="showExpansivelImprimir(500, 200,\'educar_acervo_colecao_cad_pop.php\',[], \'Coleção\')" />', false, false);

        // Idioma
        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarAcervoIdioma();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_acervo_idioma']}"] = "{$registro['nm_idioma']}";
            }
        }

        $this->campoLista('ref_cod_acervo_idioma', 'Idioma', $opcoes, $this->ref_cod_acervo_idioma, '', false, '', '<img id=\'img_idioma\' src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="showExpansivelImprimir(400, 150,\'educar_acervo_idioma_cad_pop.php\',[], \'Idioma\')" />');

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarAcervoEditora();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_acervo_editora']}"] = "{$registro['nm_editora']}";
            }
        }

        $this->campoLista('ref_cod_acervo_editora', 'Editora', $opcoes, $this->ref_cod_acervo_editora, '', false, '', '<img id=\'img_editora\' src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="showExpansivelImprimir(400, 320,\'educar_acervo_editora_cad_pop.php\',[], \'Editora\')" />');

        //-----------------------INCLUI AUTOR------------------------//

        $opcoes = [ '' => 'Selecione', 1 => 'Autor - Nome pessoal', 2 => 'Autor - Evento', 3 => 'Autor - Entidade coletiva', 4 => 'Obra anônima'];
        $this->campoLista('ref_cod_tipo_autor', 'Tipo de autor', $opcoes, $this->ref_cod_tipo_autor, false, true, false, false, false, false);
        $this->campoTexto('tipo_autor', '', $this->tipo_autor, 40, 255, false);

        $options       = ['label' => 'Autores', 'multiple' => true, 'required' => false, 'options' => ['value' => null]];

        $this->inputsHelper()->select('autores[]', $options);
        //$this->inputsHelper()->multipleSearchAutores('', $options, $helperOptions);

        // text
        $this->campoTexto('titulo', 'T&iacute;tulo', $this->titulo, 40, 255, true);
        $this->campoTexto('sub_titulo', 'Subt&iacute;tulo', $this->sub_titulo, 40, 255, false);
        $this->campoTexto('estante', 'Estante', $this->estante, 20, 15, false);
        $this->campoTexto('dimencao', 'Dimensão', $this->dimencao, 20, 255, false);
        $this->campoTexto('material_ilustrativo', 'Material ilustrativo', $this->material_ilustrativo, 20, 255, false);
        //$this->campoTexto( "dimencao_ilustrativo", "Dimensão da ilustração", $this->dimencao_ilustrativo, 20, 255, false );
        $this->campoTexto('local', 'Local', $this->local, 20, 255, false);

        $helperOptions = ['objectName' => 'assuntos'];
        $options       = ['label' => 'Assuntos', 'size' => 50, 'required' => false, 'options' => ['value' => null]];
        $this->inputsHelper()->multipleSearchAssuntos('', $options, $helperOptions);

        $helperOptions = ['objectName' => 'categorias'];
        $options       = ['label' => 'Categorias', 'size' => 50, 'required' => false, 'options' => ['value' => null]];
        $this->inputsHelper()->multipleSearchCategoriaObra('', $options, $helperOptions);

        $this->campoTexto('cdd', 'CDD', $this->cdd, 20, 15, false);
        $this->campoTexto('cdu', 'CDU', $this->cdu, 20, 15, false);
        $this->campoTexto('cutter', 'Cutter', $this->cutter, 20, 15, false);
        $this->campoNumero('volume', 'Volume', $this->volume, 20, 255, false);
        $this->campoNumero('num_edicao', 'N&uacute;mero Edic&atilde;o', $this->num_edicao, 20, 255, false);
        $this->campotexto('ano', 'Ano', $this->ano, 25, 25, false);
        $this->campoNumero('num_paginas', 'N&uacute;mero P&aacute;ginas', $this->num_paginas, 5, 255, false);
        $this->campoTexto('isbn', 'ISBN', $this->isbn, 20, 13, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_acervo_lst.php');

        $obj = new clsPmieducarAcervo(null, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo, null, $this->pessoa_logada, $this->ref_cod_acervo_colecao, $this->ref_cod_acervo_idioma, $this->ref_cod_acervo_editora, $this->titulo, $this->sub_titulo, $this->cdu, $this->cutter, $this->volume, $this->num_edicao, $this->ano, $this->num_paginas, $this->isbn, null, null, 1, $this->ref_cod_biblioteca, $this->cdd, $this->estante, $this->dimencao, $this->material_ilustrativo, null, $this->local, $this->ref_cod_tipo_autor, $this->tipo_autor);
        $this->cod_acervo = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_acervo = $this->cod_acervo;
            #cadastra assuntos para a obra
            $this->gravaAssuntos($cadastrou);
            $this->gravaAutores($cadastrou);
            $this->gravaCategorias($cadastrou);

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_lst.php');
        }
        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_acervo_lst.php');

        $obj = new clsPmieducarAcervo($this->cod_acervo, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo, $this->pessoa_logada, null, $this->ref_cod_acervo_colecao, $this->ref_cod_acervo_idioma, $this->ref_cod_acervo_editora, $this->titulo, $this->sub_titulo, $this->cdu, $this->cutter, $this->volume, $this->num_edicao, $this->ano, $this->num_paginas, $this->isbn, null, null, 1, $this->ref_cod_biblioteca, $this->cdd, $this->estante, $this->dimencao, $this->material_ilustrativo, null, $this->local, $this->ref_cod_tipo_autor, $this->tipo_autor);
        $editou = $obj->edita();
        if ($editou) {
            #cadastra assuntos para a obra
            $this->gravaAssuntos($this->cod_acervo);
            $this->gravaAutores($this->cod_acervo);
            $this->gravaCategorias($this->cod_acervo);

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_lst.php');
        }
        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(598, $this->pessoa_logada, 11, 'educar_acervo_lst.php');

        $obj = new clsPmieducarAcervo($this->cod_acervo, null, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0, $this->ref_cod_biblioteca);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $objCategoria = new clsPmieducarCategoriaAcervo();
            $objCategoria->deletaCategoriaDaObra($this->cod_acervo);
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function gravaAssuntos($cod_acervo)
    {
        $objAssunto = new clsPmieducarAcervoAssunto();
        $objAssunto->deletaAssuntosDaObra($cod_acervo);
        foreach ($this->getRequest()->assuntos as $assuntoId) {
            if (! empty($assuntoId)) {
                $objAssunto = new clsPmieducarAcervoAssunto();
                $objAssunto->cadastraAssuntoParaObra($cod_acervo, $assuntoId);
            }
        }
    }

    public function gravaCategorias($cod_acervo)
    {
        $objCategoria = new clsPmieducarCategoriaAcervo();
        $objCategoria->deletaCategoriaDaObra($cod_acervo);
        foreach ($this->getRequest()->categorias as $categoriaId) {
            if (!empty($categoriaId)) {
                $objCategoria = new clsPmieducarCategoriaAcervo();
                $objCategoria->cadastraCategoriaParaObra($cod_acervo, $categoriaId);
            }
        }
    }

    public function gravaAutores($cod_acervo)
    {
        $objAutor = new clsPmieducarAcervoAcervoAutor();
        $objAutor->deletaAutoresDaObra($cod_acervo);

        $principal = 0;

        foreach ($this->getRequest()->autores as $autorId) {
            if (! empty($autorId)) {
                $principal += 1;
                $objAutor = new clsPmieducarAcervoAcervoAutor();
                $objAutor->cadastraAutorParaObra($cod_acervo, $autorId, $principal);
            }
        }
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/acervo-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Obras';
        $this->processoAp = '598';
    }
};
