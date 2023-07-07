<?php

use App\Models\WithdrawalReason;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_motivo_afastamento;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_motivo;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_motivo_afastamento = $_GET['cod_motivo_afastamento'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 633, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_motivo_afastamento_lst.php');

        if (is_numeric($this->cod_motivo_afastamento)) {
            $registro = WithdrawalReason::find($this->cod_motivo_afastamento)?->getAttributes();

            if ($registro) {
                foreach ($registro as $campo => $val) {    // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
                $det_escola = $obj_escola->detalhe();
                $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 633, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
                $this->ref_cod_instituicao = $registro['ref_cod_instituicao'];
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}" : 'educar_motivo_afastamento_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' motivo de afastamento', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_motivo_afastamento', valor: $this->cod_motivo_afastamento);

        // foreign keys
        $obrigatorio = true;
        $get_escola = false;
        include 'include/pmieducar/educar_campo_lista.php';

        // text
        $this->campoTexto(nome: 'nm_motivo', campo: 'Motivo de Afastamento', valor: $this->nm_motivo, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 633, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_motivo_afastamento_lst.php');

        $obj = new WithdrawalReason();
        $obj->ref_usuario_cad = $this->pessoa_logada;
        $obj->nm_motivo = $this->nm_motivo;
        $obj->descricao = $this->descricao;
        $obj->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($obj->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_afastamento_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 633,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_motivo_afastamento_lst.php'
        );

        $obj = WithdrawalReason::find($this->cod_motivo_afastamento);
        $obj->ref_usuario_exc = $this->pessoa_logada;
        $obj->nm_motivo = $this->nm_motivo;
        $obj->descricao = $this->descricao;
        $obj->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($obj->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_afastamento_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 633, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_motivo_afastamento_lst.php');

        $obj = WithdrawalReason::find($this->cod_motivo_afastamento);

        if ($obj->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_afastamento_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Motivo Afastamento';
        $this->processoAp = '633';
    }
};
