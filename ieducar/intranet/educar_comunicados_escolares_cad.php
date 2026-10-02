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

    public array $escolas = [];

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
                $this->escolas = $registro->schools()->pluck('cod_escola')->all();
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
        $this->campoLista(nome: 'escolas', campo: 'Escola(s)', valor: ['all' => 'Todas as escolas'] + $this->escolasDisponiveis(), multiple: 10);
        $this->campoOculto(nome: 'escolas_selecionadas', valor: implode(',', $this->escolas));

        // text
        $this->campoTexto(nome: 'titulo', campo: 'Título', valor: $this->titulo, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);
        $this->campoTexto(nome: 'local', campo: 'Local', valor: $this->local, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->inputsHelper()->date(attrName: 'data', inputOptions: ['label' => 'Data', 'placeholder' => 'dd/mm/yyyy', 'value' => $this->date]);
        $this->campoHora(nome: 'hora', campo: 'Hora', valor: $this->hora, obrigatorio: true);

        Portabilis_View_Helper_Application::loadChosenLib(viewInstance: $this);
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Cadastro/Assets/Javascripts/ComunicadosEscolares.js',
        ]);
    }

    private function escolasDisponiveis(): array
    {
        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $escolas = [];
            foreach (App_Model_IedFinder::getEscolasUser(cod_usuario: $this->pessoa_logada) as $escola) {
                $escolas[$escola['ref_cod_escola']] = $escola['nome'];
            }

            return $escolas;
        }

        $instituicao = request()->integer('ref_cod_instituicao') ?: ($this->ref_cod_instituicao ?: (new clsPermissoes)->getInstituicao(int_idpes_usuario: $this->pessoa_logada));

        return App_Model_IedFinder::getEscolas(instituicaoId: $instituicao);
    }

    private function obterEscolasValidadas(): ?array
    {
        $escolas = request()->input('escolas', []);
        if (!is_array($escolas)) {
            $escolas = [$escolas];
        }

        $selecionadas = array_values(array_filter(array_map('intval', $escolas)));

        if (empty($selecionadas)) {
            $this->mensagem = 'Selecione ao menos uma escola.<br>';

            return null;
        }

        $permitidas = array_map('intval', array_keys($this->escolasDisponiveis()));

        if (array_diff($selecionadas, $permitidas) !== []) {
            $this->mensagem = 'Você não tem permissão para publicar comunicados para uma ou mais escolas selecionadas.<br>';

            return null;
        }

        return $selecionadas;
    }

    public function Novo()
    {
        $escolas = $this->obterEscolasValidadas();
        if ($escolas === null) {
            return false;
        }

        $notice = SchoolNotice::create([
            'institution_id' => request()->integer('ref_cod_instituicao'),
            'user_id' => $this->pessoa_logada,
            'title' => request()->string('titulo'),
            'description' => request()->string('descricao'),
            'date' => Carbon::createFromFormat('d/m/Y', request()->string('data')),
            'hour' => request()->string('hora'),
            'local' => request()->string('local'),
        ]);

        if ($notice) {
            $notice->schools()->sync($escolas);
            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_comunicados_escolares_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $escolas = $this->obterEscolasValidadas();
        if ($escolas === null) {
            return false;
        }

        $notice = SchoolNotice::find($this->id);

        if (!$notice) {
            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }

        $notice->fill([
            'institution_id' => request()->integer('ref_cod_instituicao'),
            'title' => request()->string('titulo'),
            'description' => request()->string('descricao'),
            'date' => Carbon::createFromFormat('d/m/Y', request()->string('data')),
            'hour' => request()->string('hora'),
            'local' => request()->string('local'),
        ]);

        $notice->save();
        $notice->schools()->sync($escolas);

        $this->mensagem = 'Edição efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_comunicados_escolares_lst.php');
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

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Comunicados Escolares';
        $this->processoAp = SchoolNotice::PROCESS;
    }
};
