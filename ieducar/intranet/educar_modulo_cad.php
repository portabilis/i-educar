<?php

use App\Models\LegacyStageType;

return new class extends clsCadastro {
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
            $obj = new clsPmieducarModulo($this->cod_modulo);
            $registro = $obj->detalhe();
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
        include('include/pmieducar/educar_campo_lista.php');

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

        $obj = new clsPmieducarModulo(cod_modulo: null, ref_usuario_exc: null, ref_usuario_cad: $this->pessoa_logada, nm_tipo: $this->nm_tipo, descricao: $this->descricao, num_meses: $this->num_meses, num_semanas: $this->num_semanas, data_cadastro: null, data_exclusao: null, ativo: 1, ref_cod_instituicao: $this->ref_cod_instituicao, num_etapas: $this->num_etapas);
        $cadastrou = $obj->cadastra();
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

        $obj = new clsPmieducarModulo(cod_modulo: $this->cod_modulo, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, nm_tipo: $this->nm_tipo, descricao: $this->descricao, num_meses: $this->num_meses, num_semanas: $this->num_semanas, data_cadastro: null, data_exclusao: null, ativo: 1, ref_cod_instituicao: $this->ref_cod_instituicao, num_etapas: $this->num_etapas);
        $editou = $obj->edita();
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

        $obj = new clsPmieducarModulo(cod_modulo: $this->cod_modulo, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, nm_tipo: null, descricao: null, num_meses: null, num_semanas: null, data_cadastro: null, data_exclusao: null, ativo: 0);
        $modulo = $obj->detalhe();

        if ($this->existeEtapaNaEscola() or $this->existeEtapaNaTurma()) {
            $this->mensagem = 'Exclusão não realizada.<br>';
            $this->url_cancelar = "educar_modulo_det.php?cod_modulo={$modulo['cod_modulo']}";

            return false;
        }

        $excluiu = $obj->excluir();
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

        $obj = new clsPmieducarAnoLetivoModulo($this->cod_modulo);
        $result = $obj->lista(int_ref_cod_modulo: $this->cod_modulo);

        return !empty($result);
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
