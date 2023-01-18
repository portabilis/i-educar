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
        $this->titulo = 'Avaliação Desempenho - Detalhe';

        $this->ref_cod_servidor=$_GET['ref_cod_servidor'];
        $this->ref_ref_cod_instituicao=$_GET['ref_ref_cod_instituicao'];
        $this->sequencial=$_GET['sequencial'];

        $tmp_obj = new clsPmieducarAvaliacaoDesempenho(sequencial: $this->sequencial, ref_cod_servidor: $this->ref_cod_servidor, ref_ref_cod_instituicao: $this->ref_ref_cod_instituicao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect(url: 'educar_avaliacao_desempenho_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_ref_cod_instituicao']);
        $det_instituicao = $obj_instituicao->detalhe();
        $nm_instituicao = $det_instituicao['nm_instituicao'];

        $obj_cod_servidor = new clsPessoa_(int_idpes: $this->ref_cod_servidor);
        $det_cod_servidor = $obj_cod_servidor->detalhe();
        $nm_servidor = $det_cod_servidor['nome'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($nm_instituicao) {
                $this->addDetalhe(detalhe: [ 'Instituição', "{$nm_instituicao}"]);
            }
        }
        if ($registro['ref_cod_servidor']) {
            $this->addDetalhe(detalhe: [ 'Servidor', "{$nm_servidor}"]);
        }
        if ($registro['titulo_avaliacao']) {
            $this->addDetalhe(detalhe: [ 'Avaliação', "{$registro['titulo_avaliacao']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe(detalhe: [ 'Descrição', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->url_editar = "educar_avaliacao_desempenho_cad.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}";
        }

        $this->url_cancelar = "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da avaliação de desempenho', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avaliação Desempenho';
        $this->processoAp = '635';
    }
};
