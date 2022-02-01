<?php

return new class extends clsListagem {
    /**
     * Referência a usuário da sessão
     *
     * @var int
     */
    public $pessoa_logada = null;

    /**
     * Título no topo da página
     *
     * @var string
     */
    public $titulo = '';

    /**
     * Limite de registros por página
     *
     * @var int
     */
    public $limite = 0;

    /**
     * Início dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset = 0;

    public $idesco;
    public $descricao;

    public function Gerar()
    {
        $this->titulo = 'Escolaridade - Listagem';

        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
      'Descrição'
    ]);

        // Outros Filtros
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite-$this->limite : 0;

        $obj_escolaridade = new clsCadastroEscolaridade();
        $obj_escolaridade->setOrderby('descricao ASC');
        $obj_escolaridade->setLimite($this->limite, $this->offset);
        $lista = $obj_escolaridade->lista(
            null,
            $this->descricao
        );

        $total = $obj_escolaridade->_total;

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
          "<a href=\"educar_escolaridade_det.php?idesco={$registro['idesco']}\">{$registro['descricao']}</a>"
        ]);
            }
        }

        $this->addPaginador2('educar_escolaridade_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_escolaridade_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Escolaridade do servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Escolaridade do servidor';
        $this->processoAp = '632';
    }
};
