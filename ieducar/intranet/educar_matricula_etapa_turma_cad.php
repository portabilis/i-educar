<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;

return new class extends clsCadastro
{
    public $cod_matricula;

    public $ref_cod_aluno;

    public $etapas_educacenso;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 687);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            // Inalcançável: o `simpleRedirect()` acima interrompe a execução.
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }
    }

    public function Inicializar()
    {
        $this->cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

        $this->validaPermissao();
        $this->validaParametros();

        return 'Editar';
    }

    public function Gerar()
    {
        $this->nome_url_cancelar = 'Voltar';
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        // Início > Escola > Alunos > Nome > Matrícula > Etapa do aluno
        $registration = LegacyRegistration::find($this->cod_matricula);
        $this->breadcrumb(currentPage: 'Etapa do aluno', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
            url(path: 'intranet/educar_aluno_lst.php') => 'Alunos',
            url(path: "intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url(path: "intranet/educar_matricula_det.php?cod_matricula={$this->cod_matricula}") => 'Matrícula',
        ]);

        $this->campoOculto(nome: 'cod_matricula', valor: $this->cod_matricula);
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        if (is_numeric($this->ref_cod_aluno)) {
            $this->nm_aluno = LegacyStudent::query()
                ->active()
                ->whereStudent($this->ref_cod_aluno)
                ->joinPerson()
                ->value('pessoa.nome');
        }

        if ($this->nm_aluno) {
            $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno);
        }
        $enturmacoes = LegacyEnrollment::query()
            ->join('pmieducar.turma', 'turma.cod_turma', 'matricula_turma.ref_cod_turma')
            ->join('pmieducar.matricula', 'matricula.cod_matricula', 'matricula_turma.ref_cod_matricula')
            ->join('pmieducar.aluno', 'aluno.cod_aluno', 'matricula.ref_cod_aluno')
            ->when(is_numeric($this->cod_matricula), fn ($q) => $q->whereRegistration($this->cod_matricula))
            ->whereIn('turma.etapa_educacenso', App_Model_Educacenso::etapas_multisseriadas())
            ->where('aluno.ativo', 1)
            ->get([
                'matricula_turma.ref_cod_turma',
                'matricula_turma.sequencial',
                'matricula_turma.etapa_educacenso',
                'turma.nm_turma',
                'turma.etapa_educacenso as etapa_ensino',
            ]);

        $todasEtapasEducacenso = loadJson(file: 'educacenso_json/etapas_ensino.json');

        foreach ($enturmacoes as $enturmacao) {
            $etapasEducacenso = $this->array_filter_key(
                array: $todasEtapasEducacenso,
                callback: function ($value) use ($enturmacao) {
                    return in_array(needle: $value, haystack: App_Model_Educacenso::etapasDaTurma(etapaEnsino: $enturmacao->etapa_ensino));
                }
            );
            $etapasEducacenso = [0 => 'Nenhuma'] + $etapasEducacenso;

            $this->campoLista(nome: "etapas_educacenso[{$enturmacao->ref_cod_turma}-{$enturmacao->sequencial}]", campo: "Etapa do aluno na turma {$enturmacao->nm_turma}:", valor: $etapasEducacenso, default: $enturmacao->etapa_educacenso, obrigatorio: false);
        }
    }

    public function Editar()
    {
        $this->validaPermissao();
        $this->validaParametros();

        foreach ($this->etapas_educacenso as $codTurmaESequencial => $etapaEducacenso) {
            // Necessário pois chave é Turma + Matrícula + Sequencial
            $codTurmaESequencial = explode(separator: '-', string: $codTurmaESequencial);
            $codTurma = $codTurmaESequencial[0];
            $sequencial = $codTurmaESequencial[1];

            $enrollment = LegacyEnrollment::query()
                ->where(column: [
                    'ref_cod_matricula' => $this->cod_matricula,
                    'ref_cod_turma' => $codTurma,
                    'sequencial' => $sequencial,
                ]
                )->firstOrFail();

            $enrollment->etapa_educacenso = $etapaEducacenso;

            $enrollment->saveOrFail();
        }

        $this->mensagem = 'Etapas atualizadas com sucesso.<br>';
        $this->simpleRedirect(url: "educar_matricula_det.php?cod_matricula={$this->cod_matricula}");
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula) {
            $this->redirectNotFound(
                url: 'educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno,
                registro: 'matrícula',
                feminino: true,
                codigo: $this->cod_matricula,
                dica: 'Acesse a matrícula pela ficha do aluno.',
            );
        }
    }

    /**
     * Filtering a array by its keys using a callback.
     *
     * @param $array array The array to filter
     * @param $callback Callback The filter callback, that will get the key as first argument.
     * @return array The remaining key => value combinations from $array.
     */
    public function array_filter_key(array $array, $callback)
    {
        $matchedKeys = array_filter(array: array_keys(array: $array), callback: $callback);

        return array_intersect_key($array, array_flip(array: $matchedKeys));
    }

    public function Formular()
    {
        $this->title = 'Etapa do aluno';
        $this->processoAp = '578';
    }
};
