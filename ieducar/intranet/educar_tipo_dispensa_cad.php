<?php

use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_tipo_dispensa;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $tipo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_tipo_dispensa=$_GET['cod_tipo_dispensa'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(577, $this->pessoa_logada, 7, 'educar_tipo_dispensa_lst.php');

        if (is_numeric($this->cod_tipo_dispensa)) {
            $obj = new clsPmieducarTipoDispensa($this->cod_tipo_dispensa);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->ref_cod_instituicao = $det_ref_cod_escola['ref_cod_instituicao'];

                $this->fexcluir = $obj_permissoes->permissao_excluir(577, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_tipo_dispensa_det.php?cod_tipo_dispensa={$registro['cod_tipo_dispensa']}" : 'educar_tipo_dispensa_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de dispensa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_tipo_dispensa', $this->cod_tipo_dispensa);

        // foreign keys
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        $this->campoTexto('nm_tipo', 'Dispensa', $this->nm_tipo, 33, 255, true);
        $options = [
            'label' => 'Tipo de dispensa',
            'resources' => SelectOptions::exemptionsTypeOptions(),
            'required' => true,
            'value' => $this->tipo ?? 1

        ];
        $this->inputsHelper()->select('tipo', $options);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj = new clsPmieducarTipoDispensa(null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1, $this->ref_cod_instituicao, $this->tipo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarTipoDispensa($this->cod_tipo_dispensa, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1, $this->ref_cod_instituicao, $this->tipo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarTipoDispensa($this->cod_tipo_dispensa, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Tipo Dispensa';
        $this->processoAp = '577';
    }
};
