<?php

use App\Models\RegionalType;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $id;

    public $name;

    public $description;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->id = request('id');

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 5841, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_regional_lst.php');

        if (is_numeric($this->id)) {
            $registro = RegionalType::find($this->id)?->getAttributes();
            if (!empty($registro)) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                // ** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 5841, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
                // **

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_regional_det.php?cod_aluno_beneficio={$registro['id']}" : 'educar_regional_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' regionais', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'id', valor: $this->id);

        // text
        $this->campoTexto(nome: 'name', campo: 'Regional', valor: $this->name, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'description', campo: 'Descrição', valor: $this->description, colunas: 60, linhas: 5);
    }

    public function Novo()
    {
        $classType = new RegionalType;
        $classType->name = $this->name;
        $classType->description = $this->description;

        if ($classType->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_regional_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $classType = RegionalType::findOrFail($this->id);
        $classType->name = $this->name;
        $classType->description = $this->description;

        if ($classType->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_regional_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $classType = RegionalType::findOrFail($this->id);

        if ($classType->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_aluno_beneficio_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Regionais';
        $this->processoAp = '5841';
    }
};
