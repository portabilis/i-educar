<?php

use App\Models\LegacyDeficiency;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_deficiencia;

    public $nm_deficiencia;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_deficiencia = $_GET['cod_deficiencia'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 631, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_deficiencia_lst.php');

        if (is_numeric($this->cod_deficiencia)) {
            $registro = LegacyDeficiency::find($this->cod_deficiencia)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 631, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
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

        $this->campoOculto(nome: 'cod_deficiencia', valor: $this->cod_deficiencia);
        $this->campoTexto(nome: 'nm_deficiencia', campo: 'Deficiência', valor: $this->nm_deficiencia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
    }

    public function Novo()
    {
        $cadastrou = false;
        if (is_string($this->nm_deficiencia)) {
            LegacyDeficiency::create([
                'nm_deficiencia' => $this->nm_deficiencia,
            ]);
            $cadastrou = true;
        }

        if ($cadastrou) {
            echo "<script>
                        parent.document.getElementById('ref_cod_deficiencia').options[parent.document.getElementById('ref_cod_deficiencia').options.length] = new Option('$this->nm_deficiencia', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_deficiencia').value = '$cadastrou';
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

    public function Formular()
    {
        $this->title = 'Deficiência';
        $this->processoAp = '631';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
