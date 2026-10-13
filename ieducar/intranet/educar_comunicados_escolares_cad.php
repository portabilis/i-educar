<?php

use App\Models\SchoolNotice;
use Carbon\Carbon;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public int $id;

    public string $titulo = '';

    public string $descricao = '';

    public string $local = '';

    public string $hora = '';

    public string $date = '';

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->id = request()->integer('id');
        $this->date = Carbon::now()->format('d/m/Y');

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: SchoolNotice::PROCESS, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_comunicados_escolares_lst.php');

        if (is_numeric($this->id) && $this->id > 0) {
            $registro = SchoolNotice::findOrFail($this->id);
            if ($registro) {

                $this->ref_cod_instituicao = $registro->institution_id;
                $this->ref_cod_escola = $registro->school_id;
                $this->titulo = $registro->title;
                $this->descricao = $registro->description;
                $this->local = $registro->local;
                $this->hora = $registro->hour->format('H:i');
                $this->date = $registro->date->format('d/m/Y');

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: SchoolNotice::PROCESS, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno === 'Editar') ? "educar_comunicados_escolares_det.php?id={$registro->getkey()}" : 'educar_comunicados_escolares_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno === 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: 'Comunicados Escolares', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'id', valor: $this->id);

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: ['value' => $this->ref_cod_escola]);

        // text
        $this->campoTexto(nome: 'titulo', campo: 'Título', valor: $this->titulo, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);
        $this->campoTexto(nome: 'local', campo: 'Local', valor: $this->local, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->inputsHelper()->date(attrName: 'data', inputOptions: ['label' => 'Data', 'placeholder' => 'dd/mm/yyyy', 'value' => $this->date]);
        $this->campoHora(nome: 'hora', campo: 'Hora', valor: $this->hora, obrigatorio: true);

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Cadastro/Assets/Javascripts/ComunicadosEscolares.js',
        ]);
    }

    public function Novo()
    {
        $notice = SchoolNotice::create([
            'institution_id' => request()->integer('ref_cod_instituicao'),
            'user_id' => $this->pessoa_logada,
            'school_id' => request()->integer('ref_cod_escola'),
            'title' => request()->string('titulo'),
            'description' => request()->string('descricao'),
            'date' => Carbon::createFromFormat('d/m/Y', request()->string('data')),
            'hour' => request()->string('hora'),
            'local' => request()->string('local'),
        ]);

        if ($notice) {
            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_comunicados_escolares_lst.php');
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    public function Editar()
    {
        $notice = SchoolNotice::query()
            ->whereKey($this->id)
            ->update([
                'institution_id' => request()->integer('ref_cod_instituicao'),
                'school_id' => request()->integer('ref_cod_escola'),
                'title' => request()->string('titulo'),
                'description' => request()->string('descricao'),
                'date' => Carbon::createFromFormat('d/m/Y', request()->string('data')),
                'hour' => request()->string('hora'),
                'local' => request()->string('local'),
            ]);

        if ($notice) {
            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_comunicados_escolares_lst.php');
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    public function Excluir()
    {
        $return = SchoolNotice::query()
            ->whereKey($this->id)
            ->delete();

        if ($return) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_comunicados_escolares_lst.php');
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    public function Formular()
    {
        $this->title = 'Comunicados Escolares';
        $this->processoAp = SchoolNotice::PROCESS;
    }
};
