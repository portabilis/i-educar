<?php

use App\Models\LegacyQualification;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_habilitacao;
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

        $this->cod_habilitacao=$_GET['cod_habilitacao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(573, $this->pessoa_logada, 3, 'educar_habilitacao_lst.php');

        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_habilitacao', $this->cod_habilitacao);
        // foreign keys
        if ($_GET['precisa_lista']) {
            $get_escola = false;
            $obrigatorio = true;
            include('include/pmieducar/educar_campo_lista.php');
        } else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        // text
        $this->campoTexto('nm_tipo', 'Habilitação', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $habilitacao = new LegacyQualification();
        $habilitacao->ref_usuario_cad = $this->pessoa_logada;
        $habilitacao->nm_tipo = $this->nm_tipo;
        $habilitacao->descricao = $this->descricao;
        $habilitacao->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($habilitacao->save()) {
            echo "<script>
                        if (parent.document.getElementById('habilitacao').disabled)
                            parent.document.getElementById('habilitacao').options[0] = new Option('Selectione', '', false, false);
                        parent.document.getElementById('habilitacao').options[parent.document.getElementById('habilitacao').options.length] = new Option('$this->nm_tipo', '$habilitacao->cod_habilitacao', false, false);
                        parent.document.getElementById('habilitacao').value = '$habilitacao->cod_habilitacao';
                        parent.document.getElementById('habilitacao').disabled = false;
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

    public function makeExtra()
    {
        if (! $_GET['ref_cod_instituicao']) {
            return file_get_contents(__DIR__ . '/scripts/extra/educar-habilitacao-cad-pop.js');
        }

        return '';
    }

    public function Formular()
    {
        $this->title = 'Habilitação';
        $this->processoAp = '573';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
