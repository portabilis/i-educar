<?php

use App\Models\LegacyProject;
use App\Models\LegacyStudentProject;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_projeto;
    public $nome;
    public $observacao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_projeto=$_GET['cod_projeto'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(21250, $this->pessoa_logada, 3, 'educar_projeto_lst.php');

        if (is_numeric($this->cod_projeto)) {
            $registro = LegacyProject::find($this->cod_projeto)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(21250, $this->pessoa_logada, 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}" : 'educar_projeto_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' projeto', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_projeto', $this->cod_projeto);

        $this->campoTexto('nome', 'Nome do projeto', $this->nome, 50, 50, true);
        $this->campoMemo('observacao', 'Observação', $this->observacao, 52, 5, false);
    }

    public function Novo()
    {
        $project = new LegacyProject();
        $project->nome = $this->nome;
        $project->observacao = $this->observacao;

        if ($project->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }
        $this->mensagem = 'Cadastro não realizado.<br>';
        return false;
    }

    public function Editar()
    {
        $project = LegacyProject::find($this->cod_projeto);
        $project->nome = $this->nome;
        $project->observacao = $this->observacao;

        if ($project->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }
        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        $count = LegacyStudentProject::query()
            ->where('ref_cod_projeto', $this->cod_projeto)
            ->count();

        if ($count > 0) {
            $this->mensagem = 'Você não pode excluir esse projeto, pois ele possui alunos vinculados.<br>';
            return false;
        }

        $project = LegacyProject::find($this->cod_projeto);

        if ($project->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }
        $this->mensagem = 'Exclusão não realizada.<br>';
        return false;
    }

    public function Formular()
    {
        $this->title = 'Projeto';
        $this->processoAp = '21250';
    }
};
