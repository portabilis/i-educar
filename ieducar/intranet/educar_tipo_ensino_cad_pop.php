<?php

use App\Models\LegacyEducationType;

return new class extends clsCadastro
{
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

        $this->cod_tipo_ensino = $_GET['cod_tipo_ensino'];

        if (is_numeric($this->cod_tipo_ensino)) {
            $registro = LegacyEducationType::find($this->cod_tipo_ensino)?->getAttributes();
            if (!$registro) {
                $this->simpleRedirect('educar_tipo_ensino_lst.php');
            }

            if (!$registro['ativo']) {
                $this->simpleRedirect('educar_tipo_ensino_lst.php');
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissao->permissao_excluir(558, $this->pessoa_logada, 7);

                $retorno = 'Editar';
            }
        }
        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('cod_tipo_ensino', $this->cod_tipo_ensino);
        if ($_GET['precisa_lista']) {
            $get_escola = false;
            $obrigatorio = true;
            include 'include/pmieducar/educar_campo_lista.php';
        } else {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        $this->campoTexto('nm_tipo', 'Tipo de Ensino', $this->nm_tipo, 30, 255, true);
    }

    public function Novo()
    {
        $object = new LegacyEducationType();
        $object->ref_usuario_cad = $this->pessoa_logada;
        $object->nm_tipo = $this->nm_tipo;
        $object->ref_cod_instituicao = $this->ref_cod_instituicao;
        $object->atividade_complementar = $this->atividade_complementar;

        if ($object->save()) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_tipo_ensino').disabled)
                            parent.document.getElementById('ref_cod_tipo_ensino').options[0] = new Option('Selecione um tipo de ensino', '', false, false);
                        parent.document.getElementById('ref_cod_tipo_ensino').options[parent.document.getElementById('ref_cod_tipo_ensino').options.length] = new Option('$this->nm_tipo', '$object->cod_tipo_ensino', false, false);
                        parent.document.getElementById('ref_cod_tipo_ensino').value = '$object->cod_tipo_ensino';
                        parent.document.getElementById('ref_cod_tipo_ensino').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            exit();
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
        if (!$_GET['precisa_lista']) {
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
