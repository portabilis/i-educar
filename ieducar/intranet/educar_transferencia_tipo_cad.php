<?php

use App\Models\LegacyTransferType;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_transferencia_tipo=$_GET['cod_transferencia_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(575, $this->pessoa_logada, 7, 'educar_transferencia_tipo_lst.php');

        if (is_numeric($this->cod_transferencia_tipo)) {
            $registro = LegacyTransferType::find($this->cod_transferencia_tipo)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(575, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_transferencia_tipo_det.php?cod_transferencia_tipo={$registro['cod_transferencia_tipo']}" : 'educar_transferencia_tipo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de transferência', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_transferencia_tipo', $this->cod_transferencia_tipo);

        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_tipo', 'Motivo Transferência', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('desc_tipo', 'Descrição', $this->desc_tipo, 60, 5, false);
    }

    public function Novo()
    {
        $object = new LegacyTransferType();
        $object->ref_usuario_cad = $this->pessoa_logada;
        $object->nm_tipo = $this->nm_tipo;
        $object->desc_tipo = $this->desc_tipo;
        $object->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($object->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $object = LegacyTransferType::find($this->cod_transferencia_tipo);
        $object->ativo = 1;
        $object->ref_usuario_exc = $this->pessoa_logada;
        $object->nm_tipo = $this->nm_tipo;
        $object->desc_tipo = $this->desc_tipo;
        $object->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($object->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        $object = LegacyTransferType::find($this->cod_transferencia_tipo);
        $object->ativo = 0;
        $object->ref_usuario_exc = $this->pessoa_logada;

        if ($object->save()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';
        return false;
    }

    public function Formular()
    {
        $this->title = 'Motivo Transferência';
        $this->processoAp = '575';
    }
};
