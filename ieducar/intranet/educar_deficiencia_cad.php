<?php

use App\Models\LegacyDeficiency;
use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_deficiencia;

    public $nm_deficiencia;

    public $deficiencia_educacenso;

    public $deficiency_type_id;

    public $exigir_laudo_medico;

    public $acao_enviar = 'acaoEnviar()';

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

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}" : 'educar_deficiencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' deficiência ou transtorno', breadcrumbs: [
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
        $this->campoTexto(nome: 'nm_deficiencia', campo: 'Deficiência ou transtorno', valor: $this->nm_deficiencia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

        $options = [
            'label' => 'Tipo',
            'resources' => SelectOptions::deficiencyTypes(),
            'value' => $this->deficiency_type_id,
        ];
        $this->inputsHelper()->select(attrName: 'deficiency_type_id', inputOptions: $options);

        $options = [
            'label' => 'Deficiência educacenso',
            'resources' => SelectOptions::educacensoDeficiencies(),
            'value' => $this->deficiencia_educacenso,
            'label_hint' => 'Deficiências definidas como "Outras" não serão exportadas no arquivo do Censo',
            'required' => false,
        ];

        $this->inputsHelper()->select(attrName: 'deficiencia_educacenso', inputOptions: $options);
        $this->campoCheck(nome: 'exigir_laudo_medico', campo: 'Exigir laudo médico?', valor: dbBool($this->exigir_laudo_medico));
        $this->campoCheck(nome: 'desconsidera_regra_diferenciada', campo: 'Desconsiderar deficiência ou transtorno na regra de avaliação diferenciada', valor: dbBool($this->desconsidera_regra_diferenciada));
    }

    public function Novo()
    {
        $cadastrou = false;
        if (is_string($this->nm_deficiencia)) {
            $cadastrou = LegacyDeficiency::create([
                'nm_deficiencia' => $this->nm_deficiencia,
                'deficiencia_educacenso' => is_numeric($this->deficiencia_educacenso) ? $this->deficiencia_educacenso : null,
                'deficiency_type_id' => is_numeric($this->deficiency_type_id) ? $this->deficiency_type_id : null,
                'desconsidera_regra_diferenciada' => !is_null($this->desconsidera_regra_diferenciada),
                'exigir_laudo_medico' => !is_null($this->exigir_laudo_medico),
            ]);
        }

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $editou = false;
        if (is_numeric($this->cod_deficiencia)) {
            $obj = LegacyDeficiency::find($this->cod_deficiencia);
            $obj->fill([
                'nm_deficiencia' => $this->nm_deficiencia,
                'deficiencia_educacenso' => is_numeric($this->deficiencia_educacenso) ? $this->deficiencia_educacenso : null,
                'deficiency_type_id' => is_numeric($this->deficiency_type_id) ? $this->deficiency_type_id : null,
                'desconsidera_regra_diferenciada' => !is_null($this->desconsidera_regra_diferenciada),
                'exigir_laudo_medico' => !is_null($this->exigir_laudo_medico),
            ]);
            $editou = $obj->save();
        }

        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = LegacyDeficiency::find($this->cod_deficiencia);

        if ($obj->individuals()->exists()) {
            $this->mensagem = 'Exclusão não realizada, pois existem pessoas físicas com a deficiência.<br>';

            return false;
        }

        $excluiu = $obj->delete();
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
