<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_acervo_idioma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_idioma;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11, 'educar_acervo_idioma_lst.php');

        return $retorno;
    }

    public function Gerar()
    {
        echo '<script>window.onload=function(){parent.EscondeDiv(\'LoadImprimir\')}</script>';
        // primary keys
        $this->campoOculto('cod_acervo_idioma', $this->cod_acervo_idioma);
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);
        // text
        $this->campoTexto('nm_idioma', 'Idioma', $this->nm_idioma, 30, 255, true);

        // data
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11, 'educar_acervo_idioma_lst.php');

        $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
        $this->cod_acervo_idioma = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_acervo_idioma = $this->cod_acervo_idioma;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            echo "<script>
                    parent.document.getElementById('idioma').value = '$cadastrou';
                    parent.document.getElementById('tipoacao').value = '';
                    parent.document.getElementById('formcadastro').submit();
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

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-acervo-autor-cad-pop.js');
    }

    public function Formular()
    {
        $this->title = 'Idioma';
        $this->processoAp = '590';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
