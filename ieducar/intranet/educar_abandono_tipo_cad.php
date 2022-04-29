<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_abandono_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nome;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_abandono_tipo=$_GET['cod_abandono_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(950, $this->pessoa_logada, 7, 'educar_abandono_tipo_lst.php');

        if (is_numeric($this->cod_abandono_tipo)) {
            $obj = new clsPmieducarAbandonoTipo();
            $lst  = $obj->lista($this->cod_abandono_tipo);
            $registro  = array_shift($lst);
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(950, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_abandono_tipo_det.php?cod_abandono_tipo={$registro['cod_abandono_tipo']}" : 'educar_abandono_tipo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de abandono', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_abandono_tipo', $this->cod_abandono_tipo);

        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nome', 'Motivo Abandono', $this->nome, 30, 255, true);
    }

    public function Novo()
    {
        $obj = new clsPmieducarAbandonoTipo(
            null,
            null,
            $this->pessoa_logada,
            $this->nome,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarAbandonoTipo($this->cod_abandono_tipo, $this->pessoa_logada, null, $this->nome, null, null, 1, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarAbandonoTipo($this->cod_abandono_tipo, $this->pessoa_logada);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Motivo Abandono';
        $this->processoAp = '950';
    }
};
