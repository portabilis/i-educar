<?php

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_cod_instituicao;

    public $ref_ano;

    public $ano;

    public $data_inicio;

    public $data_fim;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $this->ref_ano = $_GET['ref_ano'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 21251, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_bloqueio_ano_letivo_lst.php');

        if (is_numeric(value: $this->ref_cod_instituicao) && is_numeric(value: $this->ref_ano)) {
            $obj = new clsPmieducarBloqueioAnoLetivo(ref_cod_instituicao: $this->ref_cod_instituicao, ref_ano: $this->ref_ano);
            $registro = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->ano = $this->ref_ano;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 21251, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']}" : 'educar_bloqueio_ano_letivo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' bloqueio ano letivo', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'ano']);
        $this->inputsHelper()->date(attrName: 'data_inicio', inputOptions: ['label' => 'Data inicial permitida', 'value' => dataToBrasil(data_original: $this->data_inicio), 'placeholder' => '']);
        $this->inputsHelper()->date(attrName: 'data_fim', inputOptions: ['label' => 'Data final permitida', 'value' => dataToBrasil(data_original: $this->data_fim), 'placeholder' => '']);
    }

    public function Novo()
    {
        $this->ref_ano = $this->ano;

        $obj = new clsPmieducarBloqueioAnoLetivo(ref_cod_instituicao: $this->ref_cod_instituicao, ref_ano: $this->ref_ano, data_inicio: dataToBanco(data_original: $this->data_inicio), data_fim: dataToBanco(data_original: $this->data_fim));
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_bloqueio_ano_letivo_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->ref_ano = $this->ano;

        $obj = new clsPmieducarBloqueioAnoLetivo(ref_cod_instituicao: $this->ref_cod_instituicao, ref_ano: $this->ref_ano, data_inicio: dataToBanco(data_original: $this->data_inicio), data_fim: dataToBanco(data_original: $this->data_fim));
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_bloqueio_ano_letivo_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $this->ref_ano = $this->ano;

        $obj = new clsPmieducarBloqueioAnoLetivo(ref_cod_instituicao: $this->ref_cod_instituicao, ref_ano: $this->ref_ano);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_bloqueio_ano_letivo_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Bloqueio do ano letivo';
        $this->processoAp = '21251';
    }
};
