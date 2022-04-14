<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $idesco;
    public $descricao;
    public $escolaridade;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->idesco=$_GET['idesco'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 4, 'educar_escolaridade_lst.php');

        if (is_numeric($this->idesco)) {
            $obj = new clsCadastroEscolaridade($this->idesco);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(632, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_escolaridade_det.php?idesco={$registro["idesco"]}" : "educar_escolaridade_lst.php";
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('idesco', $this->idesco);

        // text
        $this->campoTexto('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 30, 255, true);

        $resources = [1 => 'Fundamental incompleto',
                           2 => 'Fundamental completo',
                           3 => 'Ensino médio - Normal/Magistério',
                           4 => 'Ensino médio - Normal/Magistério Indígena',
                           5 => 'Ensino médio',
                           6 => 'Superior'];

        $options = ['label' => 'Escolaridade', 'resources' => $resources, 'value' => $this->escolaridade];
        $this->inputsHelper()->select('escolaridade', $options);
    }

    public function Novo()
    {
        $obj = new clsCadastroEscolaridade(null, $this->descricao, $this->escolaridade);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        parent.document.getElementById('ref_idesco').options[parent.document.getElementById('ref_idesco').options.length] = new Option('$this->descricao', '$cadastrou', false, false);
                        parent.document.getElementById('ref_idesco').value = '$cadastrou';
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();

            return true;
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

    public function Formular()
    {
        $this->title = 'Escolaridade';
        $this->processoAp = '632';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
