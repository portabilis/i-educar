<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_nivel_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_nivel;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_nivel_ensino=$_GET['cod_nivel_ensino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(571, $this->pessoa_logada, 3, 'educar_nivel_ensino_lst.php');

        if (is_numeric($this->cod_nivel_ensino)) {
            $obj = new clsPmieducarNivelEnsino($this->cod_nivel_ensino);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(571, $this->pessoa_logada, 3);
                $retorno = 'Editar';
            }
        }
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_nivel_ensino', $this->cod_nivel_ensino);

        // foreign keys
        if ($_GET['precisa_lista']) {
            $obrigatorio = true;
            include('include/pmieducar/educar_campo_lista.php');
        } else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        // text
        $this->campoTexto('nm_nivel', 'Nível Ensino', $this->nm_nivel, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj = new clsPmieducarNivelEnsino(null, null, $this->pessoa_logada, $this->nm_nivel, $this->descricao, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_nivel_ensino').disabled)
                            parent.document.getElementById('ref_cod_nivel_ensino').options[0] = new Option('Selecione um nível de ensino', '', false, false);
                        parent.document.getElementById('ref_cod_nivel_ensino').options[parent.document.getElementById('ref_cod_nivel_ensino').options.length] = new Option('$this->nm_nivel', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_nivel_ensino').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_nivel_ensino').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();

            return true;
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

    public function makeExtra()
    {
        if (! $_GET['ref_cod_instituicao']) {
            return file_get_contents(__DIR__ . '/scripts/extra/educar-habilitacao-cad-pop.js');
        }

        return '';
    }

    public function Formular()
    {
        $this->title = 'Nível Ensino';
        $this->processoAp = '571';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
