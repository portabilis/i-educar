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

    public $cod_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $abreviatura;
    public $professor;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Função - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Nome Func&atilde;o',
            'Abreviatura',
            'Professor'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_funcao', 'Nome Fun&ccedil;&atilde;o', $this->nm_funcao, 30, 255, false);
        $this->campoTexto('abreviatura', 'Abreviatura', $this->abreviatura, 30, 255, false);
        $opcoes = ['' => 'Selecione',
                        'N' => 'N&atilde;o',
                        'S' => 'Sim'
                        ];

        $this->campoLista('professor', 'Professor', $opcoes, $this->professor, '', false, '', '', false, false);

        if ($this->professor == 'N') {
            $this->professor =  '0';
        } elseif ($this->professor == 'S') {
            $this->professor = '1';
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_funcao = new clsPmieducarFuncao();
        $obj_funcao->setOrderby('nm_funcao ASC');
        $obj_funcao->setLimite($this->limite, $this->offset);

        $lista = $obj_funcao->lista(
            $this->cod_funcao,
            null,
            null,
            $this->nm_funcao,
            $this->abreviatura,
            $this->professor,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_funcao->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $registro['professor'] = $registro['professor'] == 1 ? 'Sim' : 'N&atilde;o';

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $nm_instituicao = $det_ref_cod_instituicao['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$registro['nm_funcao']}</a>",
                    "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$registro['abreviatura']}</a>",
                    "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$registro['professor']}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$nm_instituicao}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_funcao_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(634, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_funcao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Funções do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Funções do servidor';
        $this->processoAp = '634';
    }
};
