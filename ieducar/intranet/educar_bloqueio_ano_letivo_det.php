<?php

return new class extends clsDetalhe
{
    public $titulo;

    public $ref_cod_instituicao;

    public $ref_ano;

    public $data_inicio;

    public $data_fim;

    public function Gerar()
    {
        $this->titulo = 'Bloqueio do ano letivo - Detalhe';

        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $this->ref_ano = $_GET['ref_ano'];

        $tmp_obj = new clsPmieducarBloqueioAnoLetivo(ref_cod_instituicao: $this->ref_cod_instituicao, ref_ano: $this->ref_ano);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_bloqueio_ano_letivo_lst.php');
        }

        if ($registro['instituicao']) {
            $this->addDetalhe(detalhe: ['Instituição', "{$registro['instituicao']}"]);
        }
        if ($registro['ref_ano']) {
            $this->addDetalhe(detalhe: ['Ano', "{$registro['ref_ano']}"]);
        }
        if ($registro['data_inicio']) {
            $this->addDetalhe(detalhe: ['Data inicial permitida', dataToBrasil(data_original: $registro['data_inicio'])]);
        }
        if ($registro['data_fim']) {
            $this->addDetalhe(detalhe: ['Data final permitida', dataToBrasil(data_original: $registro['data_fim'])]);
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 21251, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_bloqueio_ano_letivo_cad.php';
            $this->url_editar = "educar_bloqueio_ano_letivo_cad.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']}";
        }
        //**
        $this->url_cancelar = 'educar_bloqueio_ano_letivo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do bloqueio do ano letivo', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Bloqueio do ano letivo';
        $this->processoAp = '21251';
    }
};
