<?php

use App\Models\PerformanceEvaluation;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

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

    public $id;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_ref_cod_instituicao = $_GET['ref_ref_cod_instituicao'];
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");

        if (request()->has('id')) {
            $avaliacao = PerformanceEvaluation::find(request()->integer('id'));
            if ($avaliacao) {
                $this->ref_cod_servidor = $avaliacao->employee_id;
                $this->ref_ref_cod_instituicao = $avaliacao->institution_id;
                $this->sequencial = $avaliacao->sequential;
                $this->descricao = $avaliacao->description;
                $this->titulo_avaliacao = $avaliacao->title;
                $this->ref_usuario_exc = $avaliacao->user_excluded_id;
                $this->ref_usuario_cad = $avaliacao->user_created_id;

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_avaliacao_desempenho_det.php?sequencial={$this->sequencial}&ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}" : "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' avaliação de desempenho', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'sequencial', valor: $this->sequencial);
        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
        $this->campoOculto(nome: 'ref_ref_cod_instituicao', valor: $this->ref_ref_cod_instituicao);

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $obj_instituicao = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
            $det_instituicao = $obj_instituicao->detalhe();
            $nm_instituicao = $det_instituicao['nm_instituicao'];
            $this->campoTexto(nome: 'nm_instituicao', campo: 'Instituição', valor: $nm_instituicao, tamanhovisivel: 30, tamanhomaximo: 255, evento: '', disabled: true);
        }

        $obj_cod_servidor = new clsPessoa_($this->ref_cod_servidor);
        $det_cod_servidor = $obj_cod_servidor->detalhe();
        $nm_servidor = $det_cod_servidor['nome'];

        $this->campoTexto(nome: 'nm_servidor', campo: 'Servidor', valor: $nm_servidor, tamanhovisivel: 30, tamanhomaximo: 255, evento: '', disabled: true);
        $this->campoTexto(nome: 'titulo_avaliacao', campo: 'Avaliação', valor: $this->titulo_avaliacao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5, obrigatorio: true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");

        $performance = new PerformanceEvaluation();
        $performance->employee_id = $this->ref_cod_servidor;
        $performance->institution_id = $this->ref_ref_cod_instituicao;
        $performance->title = $this->titulo_avaliacao;
        $performance->description = $this->descricao;
        $performance->created_by = $this->pessoa_logada;

        if ($performance->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $avaliacao = PerformanceEvaluation::find(request('id'));
        if ($avaliacao) {
            $avaliacao->title = $this->titulo_avaliacao;
            $avaliacao->description = $this->descricao;

            if ($avaliacao->save()) {
                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
            }
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");

        $avaliacao = PerformanceEvaluation::find(request('id'));
        if ($avaliacao->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avaliação Desempenho';
        $this->processoAp = '635';
    }
};
