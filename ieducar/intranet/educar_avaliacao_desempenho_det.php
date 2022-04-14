<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $sequencial;
    public $ref_cod_servidor;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $titulo_avaliacao;
    public $ref_ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Avalia&ccedil;&atilde;o Desempenho - Detalhe';

        $this->ref_cod_servidor=$_GET['ref_cod_servidor'];
        $this->ref_ref_cod_instituicao=$_GET['ref_ref_cod_instituicao'];
        $this->sequencial=$_GET['sequencial'];

        $tmp_obj = new clsPmieducarAvaliacaoDesempenho($this->sequencial, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_avaliacao_desempenho_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_ref_cod_instituicao']);
        $det_instituicao = $obj_instituicao->detalhe();
        $nm_instituicao = $det_instituicao['nm_instituicao'];

        $obj_cod_servidor = new clsPessoa_($this->ref_cod_servidor);
        $det_cod_servidor = $obj_cod_servidor->detalhe();
        $nm_servidor = $det_cod_servidor['nome'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($nm_instituicao) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$nm_instituicao}"]);
            }
        }
        if ($registro['ref_cod_servidor']) {
            $this->addDetalhe([ 'Servidor', "{$nm_servidor}"]);
        }
        if ($registro['titulo_avaliacao']) {
            $this->addDetalhe([ 'Avalia&ccedil;&atilde;o', "{$registro['titulo_avaliacao']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->url_novo = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->url_editar = "educar_avaliacao_desempenho_cad.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}";
        }

        $this->url_cancelar = "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da avaliação de desempenho', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avalia&ccedil;&atilde;o Desempenho';
        $this->processoAp = '635';
    }
};
