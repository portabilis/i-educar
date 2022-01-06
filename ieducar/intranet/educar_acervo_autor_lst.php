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

    public $cod_acervo_autor;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_autor;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        $this->titulo = 'Autor - Listagem';
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        //$this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        //  $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        switch ($nivel_usuario) {
            case 1:
                $this->addCabecalhos([
                    'Autor',
                    'Biblioteca',
                    'Escola',
                    'Institui&ccedil;&atilde;o',
                ]);
            break;
            case 2:
                $this->addCabecalhos([
                    'Autor',
                    'Escola'
                ]);
            break;
            case 4:
                $this->addCabecalhos([
                    'Autor'
                ]);
            break;
            default:
                $this->addCabecalhos([
                    'Autor'
                ]);
                break;
        }

        // outros Filtros
        $this->campoTexto('nm_autor', 'Autor', $this->nm_autor, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_acervo_autor = new clsPmieducarAcervoAutor();
        $obj_acervo_autor->setOrderby('nm_autor ASC');
        $obj_acervo_autor->setLimite($this->limite, $this->offset);

        $lista = $obj_acervo_autor->lista(
            null,
            null,
            null,
            $this->nm_autor,
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

        $total = $obj_acervo_autor->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_biblioteca = $obj_biblioteca->detalhe();

                $obj_ref_cod_escola = new clsPmieducarEscola($det_biblioteca['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];

                switch ($nivel_usuario) {
                    case 1:
                        $obj_ref_cod_escola = new clsPmieducarEscola($det_biblioteca['ref_cod_escola']);
                        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                        $registro['ref_cod_instituicao'] = $det_ref_cod_escola['ref_cod_instituicao'];

                        $obj_ref_cod_intituicao = new clsPmieducarInstituicao($det_biblioteca['ref_cod_instituicao']);
                        $det_ref_cod_intituicao = $obj_ref_cod_intituicao->detalhe();
                        $registro['ref_cod_instituicao'] = $det_ref_cod_intituicao['nm_instituicao'];

                        $this->addLinhas([
                            "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$registro['nm_autor']}</a>",
                            "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$det_biblioteca['nm_biblioteca']}</a>",
                            "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$registro['ref_cod_escola']}</a>",
                            "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$registro['ref_cod_instituicao']}</a>"
                        ]);

                        break;
                    case 2:
                    $this->addLinhas([
                        "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$registro['nm_autor']}</a>",
                        "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$registro['ref_cod_escola']}</a>"
                    ]);
                    break;
                    case 4:
                    default:
                    $this->addLinhas([
                        "<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}\">{$registro['nm_autor']}</a>"
                    ]);
                    break;

                }
            }
        }
        $this->addPaginador2('educar_acervo_autor_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_acervo_autor_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de autores', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Autor';
        $this->processoAp = '594';
    }
};
