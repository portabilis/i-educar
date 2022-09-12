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

    public $cod_tipo_usuario;
    public $ref_funcionario_cad;
    public $ref_funcionario_exc;
    public $nm_tipo;
    public $descricao;
    public $nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Tipo Usuario - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Código Tipo Usuário',
            'Tipo Usuário',
            'Descrição',
            'Nível',
        ]);

        //niveis
        $array_nivel = ['-1' => 'Selecione'] + $this->user()->type->getLevelDescriptions()->toArray();

        if (!isset($this->nivel)) {
            $this->nivel = -1;
        }

        // outros Filtros
        $this->campoTexto('nm_tipo', 'Nome Tipo', $this->nm_tipo, 30, 255, false);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 30, 255, false);
        $this->campoLista('nivel', 'Nível', $array_nivel, $this->nivel, '', false, '', '', false, false);

        $this->nivel = $this->nivel == -1 ? '' : $this->nivel;

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_tipo_usuario = new clsPmieducarTipoUsuario();
        $obj_tipo_usuario->setOrderby('nm_tipo ASC');
        $obj_tipo_usuario->setLimite($this->limite, $this->offset);

        $lista = $obj_tipo_usuario->lista(
            null,
            null,
            null,
            $this->nm_tipo,
            $this->descricao,
            $this->nivel,
            null,
            null,
            1,
            $this->user()->type->level
        );

        $total = $obj_tipo_usuario->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {

                // pega detalhes de foreign_keys

                $url = route('usertype.show', ['userType' => $registro['cod_tipo_usuario']]);

                $this->addLinhas([
                    "<a href=\"{$url}\">{$registro['cod_tipo_usuario']}</a>",
                    "<a href=\"{$url}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"{$url}\">{$registro['descricao']}</a>",
                    "<a href=\"{$url}\">{$array_nivel[$registro['nivel']]}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_tipo_usuario_lst.php', $total, $_GET, $this->nome, $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(554, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("' . route('usertype.new') . '")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de tipo de usuário', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo Usuario';
        $this->processoAp = '554';
    }
};
