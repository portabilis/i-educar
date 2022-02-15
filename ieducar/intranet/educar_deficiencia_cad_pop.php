<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_deficiencia;
    public $nm_deficiencia;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_deficiencia=$_GET['cod_deficiencia'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(631, $this->pessoa_logada, 7, 'educar_deficiencia_lst.php');

        if (is_numeric($this->cod_deficiencia)) {
            $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(631, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {

        $this->campoOculto('cod_deficiencia', $this->cod_deficiencia);
        $this->campoTexto('nm_deficiencia', 'Deficiência', $this->nm_deficiencia, 30, 255, true);
    }

    public function Novo()
    {
        $obj = new clsCadastroDeficiencia($this->cod_deficiencia, $this->nm_deficiencia);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        parent.document.getElementById('ref_cod_deficiencia').options[parent.document.getElementById('ref_cod_deficiencia').options.length] = new Option('$this->nm_deficiencia', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_deficiencia').value = '$cadastrou';
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function Formular()
    {
        $this->title = 'Deficiência';
        $this->processoAp = '631';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
