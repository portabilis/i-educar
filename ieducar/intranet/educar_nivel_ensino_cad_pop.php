<?php

use App\Models\LegacyEducationLevel;

return new class extends clsCadastro
{
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

        $this->cod_nivel_ensino = $_GET['cod_nivel_ensino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 571, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_nivel_ensino_lst.php');

        if (is_numeric($this->cod_nivel_ensino)) {
            $registro = LegacyEducationLevel::find($this->cod_nivel_ensino)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 571, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
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
        $this->campoOculto(nome: 'cod_nivel_ensino', valor: $this->cod_nivel_ensino);

        // foreign keys
        if ($_GET['precisa_lista']) {
            $obrigatorio = true;
            include 'include/pmieducar/educar_campo_lista.php';
        } else {
            $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
        }
        // text
        $this->campoTexto(nome: 'nm_nivel', campo: 'Nível Ensino', valor: $this->nm_nivel, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);
    }

    public function Novo()
    {
        $level = new LegacyEducationLevel();
        $level->ref_usuario_cad = $this->pessoa_logada;
        $level->nm_nivel = $this->nm_nivel;
        $level->descricao = $this->descricao;
        $level->ref_cod_instituicao = $this->ref_cod_instituicao;

        if ($level->save()) {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_nivel_ensino').disabled)
                            parent.document.getElementById('ref_cod_nivel_ensino').options[0] = new Option('Selecione um nível de ensino', '', false, false);
                        parent.document.getElementById('ref_cod_nivel_ensino').options[parent.document.getElementById('ref_cod_nivel_ensino').options.length] = new Option('$this->nm_nivel', '$level->cod_nivel_ensino', false, false);
                        parent.document.getElementById('ref_cod_nivel_ensino').value = '$level->cod_nivel_ensino';
                        parent.document.getElementById('ref_cod_nivel_ensino').disabled = false;
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
        if (!$_GET['ref_cod_instituicao']) {
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
