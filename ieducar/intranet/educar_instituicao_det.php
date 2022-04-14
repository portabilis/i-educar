<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_instituicao;
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
    public $pessoa_logada;

    public function Gerar()
    {
        $this->titulo = 'Institui&ccedil;&atilde;o - Detalhe';

        $this->cod_instituicao=$_GET['cod_instituicao'];

        $tmp_obj = new clsPmieducarInstituicao($this->cod_instituicao);
        $registro = $tmp_obj->detalhe();

        $registro['cep'] = int2CEP($registro['cep']);
        $this->addDetalhe([ 'Código Instituição', "{$registro['cod_instituicao']}"]);
        $this->addDetalhe([ 'Nome da Instituição', "{$registro['nm_instituicao']}"]);
        $this->addDetalhe([ 'CEP', "{$registro['cep']}"]);
        $this->addDetalhe([ 'Logradouro', "{$registro['logradouro']}"]);
        $this->addDetalhe([ 'Bairro', "{$registro['bairro']}"]);
        $this->addDetalhe([ 'Cidade', "{$registro['cidade']}"]);
        $this->addDetalhe([ 'Tipo do Logradouro', "{$registro['ref_idtlog']}"]);
        $this->addDetalhe([ 'UF', "{$registro['ref_sigla_uf']}"]);
        $this->addDetalhe([ 'Número', "{$registro['numero']}"]);
        $this->addDetalhe([ 'Complemento', "{$registro['complemento']}"]);
        $this->addDetalhe([ 'DDD Telefone', "{$registro['ddd_telefone']}"]);
        $this->addDetalhe([ 'Telefone', "{$registro['telefone']}"]);
        $this->addDetalhe([ 'Nome do Responsável', "{$registro['nm_responsavel']}"]);

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(559, $this->pessoa_logada, 3)) {
            $this->url_editar = "educar_instituicao_cad.php?cod_instituicao={$registro['cod_instituicao']}";
        }
        $this->url_cancelar = 'educar_instituicao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da instituição', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->array_botao[] = 'Documentação padrão';
        $this->array_botao_url_script[] = "go(\"educar_documentacao_instituicao_cad.php?cod_instituicao={$registro['cod_instituicao']}\")";
    }

    public function Formular()
    {
        $this->title = 'Institui&ccedil;&atilde;o';
        $this->processoAp = '559';
    }
};
