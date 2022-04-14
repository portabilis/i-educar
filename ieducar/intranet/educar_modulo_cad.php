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
            584,
            $this->pessoa_logada,
            3,
            'educar_modulo_lst.php'
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
                if ($obj_permissoes->permissao_excluir(584, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}" : 'educar_modulo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_modulo', $this->cod_modulo);

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $option = false;
        if ($this->existeEtapaNaEscola() or $this->existeEtapaNaTurma()) {
            $option = true;
        }

        $this->campoTexto('nm_tipo', 'Etapa', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
        $this->campoNumero('num_etapas', 'Número de etapas', $this->num_etapas, 2, 2, true, null, null, null, null, null, $option);
        $this->campoNumero('num_meses', 'Número de meses', $this->num_meses, 2, 2, false);
        $this->campoNumero('num_semanas', 'Número de semanas', $this->num_semanas, 2, 2, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(584, $this->pessoa_logada, 3, 'educar_modulo_lst.php');

        if (LegacyStageType::alreadyExists($this->nm_tipo, $this->num_etapas)) {
            $this->mensagem = 'Já existe um registro cadastrado com o mesmo nome e o mesmo número de etapa(s).<br>';

            return false;
        }

        $obj = new clsPmieducarModulo(null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, $this->num_meses, $this->num_semanas, null, null, 1, $this->ref_cod_instituicao, $this->num_etapas);
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
        $obj_permissoes->permissao_cadastra(584, $this->pessoa_logada, 3, 'educar_modulo_lst.php');

        if (LegacyStageType::alreadyExists($this->nm_tipo, $this->num_etapas, $this->cod_modulo)) {
            $this->mensagem = 'Já existe um registro cadastrado com o mesmo nome e o mesmo número de etapa(s).<br>';

            return false;
        }

        $obj = new clsPmieducarModulo($this->cod_modulo, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, $this->num_meses, $this->num_semanas, null, null, 1, $this->ref_cod_instituicao, $this->num_etapas);
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
        $obj_permissoes->permissao_excluir(584, $this->pessoa_logada, 3, 'educar_modulo_lst.php');

        $obj = new clsPmieducarModulo($this->cod_modulo, $this->pessoa_logada, null, null, null, null, null, null, null, 0);
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
        $result = $obj->lista(null, null, null, $this->cod_modulo);

        return !empty($result);
    }

    public function existeEtapaNaTurma()
    {
        if (!$this->cod_modulo) {
            return false;
        }

        $obj = new clsPmieducarTurmaModulo($this->cod_modulo);
        $result = $obj->lista(null, $this->cod_modulo);

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
