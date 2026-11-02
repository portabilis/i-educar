<?php

return new class extends clsDetalhe
{
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
        $this->titulo = 'Instituição - Detalhe';

        $this->cod_instituicao = $_GET['cod_instituicao'];

        $tmp_obj = new clsPmieducarInstituicao($this->cod_instituicao);
        $registro = $tmp_obj->detalhe();

        $registro['cep'] = int2CEP($registro['cep']);
        $this->addDetalhe(['Código Instituição', e($registro['cod_instituicao'])]);
        $this->addDetalhe(['Nome da Instituição', e($registro['nm_instituicao'])]);
        $this->addDetalhe(['CEP', e($registro['cep'])]);
        $this->addDetalhe(['Logradouro', e($registro['logradouro'])]);
        $this->addDetalhe(['Bairro', e($registro['bairro'])]);
        $this->addDetalhe(['Cidade', e($registro['cidade'])]);
        $this->addDetalhe(['Tipo do Logradouro', e($registro['ref_idtlog'])]);
        $this->addDetalhe(['UF', e($registro['ref_sigla_uf'])]);
        $this->addDetalhe(['Número', e($registro['numero'])]);
        $this->addDetalhe(['Complemento', e($registro['complemento'])]);
        $this->addDetalhe(['DDD Telefone', e($registro['ddd_telefone'])]);
        $this->addDetalhe(['Telefone', e($registro['telefone'])]);
        $this->addDetalhe(['Nome do Responsável', e($registro['nm_responsavel'])]);

        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 559, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_editar = "educar_instituicao_cad.php?cod_instituicao={$registro['cod_instituicao']}";
        }
        $this->url_cancelar = 'educar_instituicao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da instituição', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->array_botao[] = 'Documentação padrão';
        $this->array_botao_url_script[] = "go(\"educar_documentacao_instituicao_cad.php?cod_instituicao={$registro['cod_instituicao']}\")";
    }

    public function Formular()
    {
        $this->title = 'Instituição';
        $this->processoAp = '559';
    }
};
