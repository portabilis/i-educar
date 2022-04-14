<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_tipo_ensino;
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

        //** Verificacao de permissao para exclusao
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(558, $this->pessoa_logada, 7, 'educar_tipo_ensino_lst.php');
        //**

        $this->cod_tipo_ensino=$_GET['cod_tipo_ensino'];

        if (is_numeric($this->cod_tipo_ensino)) {
            $obj = new clsPmieducarTipoEnsino($this->cod_tipo_ensino, null, null, null, null, null, 1);
            if (!$registro = $obj->detalhe()) {
                $this->simpleRedirect('educar_tipo_ensino_lst.php');
            }

            if (!$registro['ativo']) {
                $this->simpleRedirect('educar_tipo_ensino_lst.php');
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(558, $this->pessoa_logada, 7);
                //**

                $retorno = 'Editar';
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_ensino_det.php?cod_tipo_ensino={$registro["cod_tipo_ensino"]}" : "educar_tipo_ensino_lst.php";
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_tipo_ensino', $this->cod_tipo_ensino);
        if ($_GET['precisa_lista']) {
            // foreign keys
            $get_escola = false;
            $obrigatorio = true;
            include('include/pmieducar/educar_campo_lista.php');
        }// text
        else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        $this->campoTexto('nm_tipo', 'Tipo de Ensino', $this->nm_tipo, 30, 255, true);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarTipoEnsino($this->cod_tipo_ensino, null, $this->pessoa_logada, $this->nm_tipo, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_tipo_ensino').disabled)
                            parent.document.getElementById('ref_cod_tipo_ensino').options[0] = new Option('Selecione um tipo de ensino', '', false, false);
                        parent.document.getElementById('ref_cod_tipo_ensino').options[parent.document.getElementById('ref_cod_tipo_ensino').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_tipo_ensino').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_tipo_ensino').disabled = false;
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
        $this->title = 'Tipo Ensino';
        $this->processoAp = '558';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
