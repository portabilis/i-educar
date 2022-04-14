<?php

use App\Models\State;

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

    public $cod_acervo_editora;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $nm_editora;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $telefone;
    public $ddd_telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        $this->titulo = 'Editora - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Editora',
            'Estado',
            'Cidade',
            'Biblioteca'
        ]);

        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_editora', 'Editora', $this->nm_editora, 30, 255, false);

        // Filtros de Foreign Keys
        $opcoes = [ '' => 'Selecione' ] + State::getListKeyAbbreviation()->toArray();

        $this->campoLista('ref_sigla_uf', 'Estado', $opcoes, $this->ref_sigla_uf, null, null, null, null, null, false);

        // outros Filtros
        $this->campoTexto('cidade', 'Cidade', $this->cidade, 30, 60, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if (!is_numeric($this->ref_cod_biblioteca)) {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_acervo_editora = new clsPmieducarAcervoEditora();
        $obj_acervo_editora->setOrderby('nm_editora ASC');
        $obj_acervo_editora->setLimite($this->limite, $this->offset);

        $lista = $obj_acervo_editora->lista(
            null,
            null,
            null,
            null,
            $this->ref_sigla_uf,
            $this->nm_editora,
            null,
            $this->cidade,
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

        $total = $obj_acervo_editora->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $registro['ref_sigla_uf'] = State::getNameByAbbreviation($registro['ref_sigla_uf']);

                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_biblioteca = $obj_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_biblioteca['nm_biblioteca'];
                $this->addLinhas([
                    "<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro['cod_acervo_editora']}\">{$registro['nm_editora']}</a>",
                    "<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro['cod_acervo_editora']}\">{$registro['ref_sigla_uf']}</a>",
                    "<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro['cod_acervo_editora']}\">{$registro['cidade']}</a>",
                    "<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro['cod_acervo_editora']}\">{$registro['ref_cod_biblioteca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_acervo_editora_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_acervo_editora_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de editoras', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Editora';
        $this->processoAp = '595';
    }
};
