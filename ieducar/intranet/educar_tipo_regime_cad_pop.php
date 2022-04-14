<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_tipo_regime;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_tipo_regime=$_GET['cod_tipo_regime'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(568, $this->pessoa_logada, 3, 'educar_tipo_regime_lst.php');

        if (is_numeric($this->cod_tipo_regime)) {
            $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(568, $this->pessoa_logada, 3);
                //**
                $retorno = 'Editar';
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_regime_det.php?cod_tipo_regime={$registro["cod_tipo_regime"]}" : "educar_tipo_regime_lst.php";
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_tipo_regime', $this->cod_tipo_regime);

        // foreign keys
        // foreign keys
        if ($_GET['precisa_lista']) {
            $get_escola = false;
            $obrigatorio = true;
            include('include/pmieducar/educar_campo_lista.php');
        }// text
        else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        $this->campoTexto('nm_tipo', 'Nome Tipo', $this->nm_tipo, 30, 255, true);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_tipo_regime').disabled)
                            parent.document.getElementById('ref_cod_tipo_regime').options[0] = new Option('Selecione um tipo de regime', '', false, false);
                        parent.document.getElementById('ref_cod_tipo_regime').options[parent.document.getElementById('ref_cod_tipo_regime').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_tipo_regime').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_tipo_regime').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function makeExtra()
    {
        if (! $_GET['precisa_lista']) {
            return file_get_contents(__DIR__ . '/scripts/extra/educar-habilitacao-cad-pop.js');
        }

        return '';
    }

    public function Formular()
    {
        $this->title = 'Tipo Regime';
        $this->processoAp = '568';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
