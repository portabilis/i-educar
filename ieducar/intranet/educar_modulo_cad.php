<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyStageType;

return new class extends clsCadastro
{
    public $cod_modulo;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_tipo;

    public $descricao;

    public $num_etapas;

    public $num_meses;

    public $num_semanas;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_modulo = $_GET['cod_modulo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 584,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 3,
            str_pagina_redirecionar: 'educar_modulo_lst.php'
        );

        if (is_numeric($this->cod_modulo)) {
            $registro = LegacyStageType::find($this->cod_modulo)->getAttributes();
            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 584, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}" : 'educar_modulo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' etapa', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_modulo', valor: $this->cod_modulo);

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include 'include/pmieducar/educar_campo_lista.php';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $option = false;
        if ($this->existeEtapaNaEscola() or $this->existeEtapaNaTurma()) {
            $option = true;
        }

        $this->campoTexto(nome: 'nm_tipo', campo: 'Etapa', valor: $this->nm_tipo, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);
        $this->campoNumero(nome: 'num_etapas', campo: 'Número de etapas', valor: $this->num_etapas, tamanhovisivel: 2, tamanhomaximo: 2, obrigatorio: true, descricao: null, descricao2: null, script: null, evento: null, duplo: null, disabled: $option);
        $this->campoNumero(nome: 'num_meses', campo: 'Número de meses', valor: $this->num_meses, tamanhovisivel: 2, tamanhomaximo: 2);
        $this->campoNumero(nome: 'num_semanas', campo: 'Número de semanas', valor: $this->num_semanas, tamanhovisivel: 2, tamanhomaximo: 2);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 584, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_modulo_lst.php');

        if (LegacyStageType::alreadyExists(name: $this->nm_tipo, stagesNumber: $this->num_etapas)) {
            $this->mensagem = 'Já existe um registro cadastrado com o mesmo nome e o mesmo número de etapa(s).<br>';

            return false;
        }

        $obj = new LegacyStageType();

        $obj->ref_usuario_cad = $this->pessoa_logada;
        $obj->nm_tipo = $this->nm_tipo;
        $obj->num_etapas = $this->num_etapas;
        $obj->descricao = $this->descricao;

        !empty($this->num_meses) ? $obj->num_meses = $this->num_meses : $obj->num_meses = null;
        !empty($this->num_semanas) ? $obj->num_semanas = $this->num_semanas : $obj->num_semanas = null;

        $obj->ref_cod_instituicao = $this->ref_cod_instituicao;
        $obj->ativo = 1;

        $cadastrou = $obj->save();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_modulo_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 584, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_modulo_lst.php');

        if (LegacyStageType::alreadyExists(name: $this->nm_tipo, stagesNumber: $this->num_etapas, id: $this->cod_modulo)) {
            $this->mensagem = 'Já existe um registro cadastrado com o mesmo nome e o mesmo número de etapa(s).<br>';

            return false;
        }

        $obj = LegacyStageType::find($this->cod_modulo);
        $obj->ref_usuario_exc = $this->pessoa_logada;
        $obj->ref_usuario_cad = $this->pessoa_logada;
        $obj->nm_tipo = $this->nm_tipo;
        $obj->descricao = $this->descricao;
        if (!empty($this->num_etapas)) {
            $obj->num_etapas = $this->num_etapas;
        }
        !empty($this->num_meses) ? $obj->num_meses = $this->num_meses : $obj->num_meses = null;
        !empty($this->num_semanas) ? $obj->num_semanas = $this->num_semanas : $obj->num_semanas = null;

        $obj->ref_cod_instituicao = $this->ref_cod_instituicao;
        $obj->ativo = 1;

        $editou = $obj->save();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_modulo_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 584, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_modulo_lst.php');

        $obj = LegacyStageType::find($this->cod_modulo);

        if ($this->existeEtapaNaEscola() or $this->existeEtapaNaTurma()) {
            $this->mensagem = 'Exclusão não realizada.<br>';
            $this->url_cancelar = "educar_modulo_det.php?cod_modulo={$obj->getKey()}";

            return false;
        }

        $obj->ativo = 0;
        $excluiu = $obj->save();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_modulo_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function existeEtapaNaEscola()
    {
        if (!$this->cod_modulo) {
            return false;
        }

        return LegacyAcademicYearStage::query()
            ->where('ref_cod_modulo', $this->cod_modulo)
            ->exists();
    }

    public function existeEtapaNaTurma()
    {
        if (!$this->cod_modulo) {
            return false;
        }

        $obj = new clsPmieducarTurmaModulo($this->cod_modulo);
        $result = $obj->lista(int_ref_cod_modulo: $this->cod_modulo);

        if (!$result > 0) {
            return false;
        }

        return true;
    }

    public function Formular()
    {
        $this->title = 'Etapa';
        $this->processoAp = '584';
    }
};
