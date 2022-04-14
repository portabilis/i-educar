<?php

use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
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

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}" : 'educar_deficiencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' deficiência', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_deficiencia', $this->cod_deficiencia);

        // foreign keys

        // text
        $this->campoTexto('nm_deficiencia', 'Deficiência', $this->nm_deficiencia, 30, 255, true);

        $options = [
            'label' => 'Deficiência educacenso',
            'resources' => SelectOptions::educacensoDeficiencies(),
            'value' => $this->deficiencia_educacenso,
            'label_hint' => 'Deficiências definidas como "Outras" não serão exportadas no arquivo do Censo',
        ];

        $this->inputsHelper()->select('deficiencia_educacenso', $options);
        $this->campoCheck('exigir_laudo_medico', 'Exigir laudo médico?', dbBool($this->exigir_laudo_medico));
        $this->campoCheck('desconsidera_regra_diferenciada', 'Desconsiderar deficiência na regra de avaliação diferenciada', dbBool($this->desconsidera_regra_diferenciada));
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
        $obj = new clsCadastroDeficiencia($this->cod_deficiencia, $this->nm_deficiencia);
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
