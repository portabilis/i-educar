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

    public $cod_instituicao;
    public $nm_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $complemento;
    public $nm_responsavel;
    public $ddd_telefone;
    public $telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Instituição - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([ 'Nome da Instituição' ]);

        // outros Filtros
        $this->campoTexto('nm_instituicao', 'Nome da Instituição', $this->nm_instituicao, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_instituicao = new clsPmieducarInstituicao();
        $obj_instituicao->setOrderby('nm_responsavel ASC');
        $obj_instituicao->setLimite($this->limite, $this->offset);
        $lista = $obj_instituicao->lista(
            $this->cod_instituicao,
            $this->ref_sigla_uf,
            $this->cep,
            $this->cidade,
            $this->bairro,
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->nm_responsavel,
            $this->ddd_telefone,
            $this->telefone,
            $this->data_cadastro,
            $this->data_exclusao,
            1,
            $this->nm_instituicao
        );

        $total = $obj_instituicao->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"educar_instituicao_det.php?cod_instituicao={$registro['cod_instituicao']}\">{$registro['nm_instituicao']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_instituicao_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->largura = '100%';

        $this->breadcrumb('Listagem de instituições', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Instituicao';
        $this->processoAp = '559';
    }
};
