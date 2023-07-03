<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEducationLevel;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_nivel_ensino;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_nivel;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_nivel_ensino = $_GET['cod_nivel_ensino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 571, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_nivel_ensino_lst.php');

        if (is_numeric($this->cod_nivel_ensino)) {
            $registro = LegacyEducationLevel::find($this->cod_nivel_ensino)?->getAttributes();

            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 571, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_nivel_ensino_det.php?cod_nivel_ensino={$registro['cod_nivel_ensino']}" : 'educar_nivel_ensino_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' nível de ensino', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_nivel_ensino', valor: $this->cod_nivel_ensino);

        // foreign keys
        $obrigatorio = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // text
        $this->campoTexto(nome: 'nm_nivel', campo: 'Nível Ensino', valor: $this->nm_nivel, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);
    }

    public function Novo()
    {
        $level = new LegacyEducationLevel();
        $level->ref_usuario_cad = $this->pessoa_logada;
        $level->nm_nivel = $this->nm_nivel;
        $level->descricao = $this->descricao;
        $level->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($level->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_nivel_ensino_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $level = LegacyEducationLevel::findOrFail($this->cod_nivel_ensino);
        $level->ativo = 1;
        $level->ref_usuario_exc = $this->pessoa_logada;
        $level->nm_nivel = $this->nm_nivel;
        $level->descricao = $this->descricao;
        $level->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($level->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_nivel_ensino_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $count = LegacyCourse::query()
            ->where(column: 'ref_cod_nivel_ensino', operator: $this->cod_nivel_ensino)
            ->count();

        if ($count > 0) {
            $this->mensagem = 'Você não pode excluir esse Nível de Ensino, pois ele possui vínculo com Curso(s).<br>';

            return false;
        }

        $level = LegacyEducationLevel::findOrFail($this->cod_nivel_ensino);
        $level->ref_usuario_exc = $this->pessoa_logada;
        $level->ativo = 0;

        if ($level->save()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_nivel_ensino_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Nível Ensino';
        $this->processoAp = '571';
    }
};
