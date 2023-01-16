<?php

use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_deficiencia;
    public $nm_deficiencia;
    public $deficiencia_educacenso;
    public $exigir_laudo_medico;
    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_deficiencia=$_GET['cod_deficiencia'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 631, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_deficiencia_lst.php');

        if (is_numeric($this->cod_deficiencia)) {
            $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
            $registro  = $obj->detalhe();
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

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}" : 'educar_deficiencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' deficiência', breadcrumbs: [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_deficiencia', valor: $this->cod_deficiencia);

        // foreign keys

        // text
        $this->campoTexto(nome: 'nm_deficiencia', campo: 'Deficiência', valor: $this->nm_deficiencia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

        $options = [
            'label' => 'Deficiência educacenso',
            'resources' => SelectOptions::educacensoDeficiencies(),
            'value' => $this->deficiencia_educacenso,
            'label_hint' => 'Deficiências definidas como "Outras" não serão exportadas no arquivo do Censo',
        ];

        $this->inputsHelper()->select(attrName: 'deficiencia_educacenso', inputOptions: $options);
        $this->campoCheck(nome: 'exigir_laudo_medico', campo: 'Exigir laudo médico?', valor: dbBool($this->exigir_laudo_medico));
        $this->campoCheck(nome: 'desconsidera_regra_diferenciada', campo: 'Desconsiderar deficiência na regra de avaliação diferenciada', valor: dbBool($this->desconsidera_regra_diferenciada));
    }

    public function Novo()
    {
        $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
        $obj->nm_deficiencia = $this->nm_deficiencia;
        $obj->deficiencia_educacenso = $this->deficiencia_educacenso;
        $obj->desconsidera_regra_diferenciada = !is_null($this->desconsidera_regra_diferenciada);
        $obj->exigir_laudo_medico = !is_null($this->exigir_laudo_medico);

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsCadastroDeficiencia($this->cod_deficiencia);
        $obj->nm_deficiencia = $this->nm_deficiencia;
        $obj->deficiencia_educacenso = $this->deficiencia_educacenso;
        $obj->desconsidera_regra_diferenciada = !is_null($this->desconsidera_regra_diferenciada);
        $obj->exigir_laudo_medico = !is_null($this->exigir_laudo_medico);

        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsCadastroDeficiencia(cod_deficiencia: $this->cod_deficiencia, nm_deficiencia: $this->nm_deficiencia);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-deficiencia-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Deficiência';
        $this->processoAp = '631';
    }
};
