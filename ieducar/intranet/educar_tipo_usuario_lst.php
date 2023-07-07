<?php

return new class extends clsListagem
{
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

        $this->addCabecalhos(coluna: [
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
        $this->campoTexto(nome: 'nm_tipo', campo: 'Nome Tipo', valor: $this->nm_tipo, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoTexto(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoLista(nome: 'nivel', campo: 'Nível', valor: $array_nivel, default: $this->nivel, obrigatorio: false);

        $this->nivel = $this->nivel == -1 ? '' : $this->nivel;

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_tipo_usuario = new clsPmieducarTipoUsuario();
        $obj_tipo_usuario->setOrderby(strNomeCampo: 'nm_tipo ASC');
        $obj_tipo_usuario->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_tipo_usuario->lista(
            str_nm_tipo: $this->nm_tipo,
            str_descricao: $this->descricao,
            int_nivel: $this->nivel,
            int_ativo: 1,
            int_nivel_menor: $this->user()->type->level
        );

        $total = $obj_tipo_usuario->_total;

        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {

                // pega detalhes de foreign_keys

                $url = route(name: 'usertype.show', parameters: ['userType' => $registro['cod_tipo_usuario']]);

                $this->addLinhas(linha: [
                    "<a href=\"{$url}\">{$registro['cod_tipo_usuario']}</a>",
                    "<a href=\"{$url}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"{$url}\">{$registro['descricao']}</a>",
                    "<a href=\"{$url}\">{$array_nivel[$registro['nivel']]}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_tipo_usuario_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 554, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, super_usuario: true)) {
            $this->acao = 'go("' . route(name: 'usertype.new') . '")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de tipo de usuário', breadcrumbs: [
            url(path: 'intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo Usuario';
        $this->processoAp = '554';
    }
};
