<?php

use App\Models\LegacyExemptionType;

return new class extends clsCadastro
{
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

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_tipo_dispensa = $_GET['cod_tipo_dispensa'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(577, $this->pessoa_logada, 7, 'educar_tipo_dispensa_lst.php');

        if (is_numeric($this->cod_tipo_dispensa)) {
            $registro = LegacyExemptionType::find($this->cod_tipo_dispensa)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(577, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno === 'Editar') ? "educar_tipo_dispensa_det.php?cod_tipo_dispensa={$registro['cod_tipo_dispensa']}" : 'educar_tipo_dispensa_lst.php';

        $nomeMenu = $retorno === 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de dispensa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {

        $this->campoOculto('cod_tipo_dispensa', $this->cod_tipo_dispensa);

        $obrigatorio = true;
        include 'include/pmieducar/educar_campo_lista.php';

        $this->campoTexto('nm_tipo', 'Tipo Dispensa', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $object = new LegacyExemptionType();
        $object->ref_usuario_cad = $this->pessoa_logada;
        $object->nm_tipo = $this->nm_tipo;
        $object->descricao = $this->descricao;
        $object->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($object->save()) {
            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $object = LegacyExemptionType::find($this->cod_tipo_dispensa);
        $object->ativo = 1;
        $object->ref_usuario_exc = $this->pessoa_logada;
        $object->nm_tipo = $this->nm_tipo;
        $object->descricao = $this->descricao;
        $object->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($object->save()) {
            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $object = LegacyExemptionType::find($this->cod_tipo_dispensa);
        $object->ativo = 0;
        $object->ref_usuario_exc = $this->pessoa_logada;

        if ($object->save()) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');

        }
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Tipo Dispensa';
        $this->processoAp = '577';
    }
};
