<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Services\EnrollmentService;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoAluno;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

return new class extends clsCadastro
{
    public $cod_matricula;

    public $ref_cod_aluno;

    public $tipo_atendimento;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 688);

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
        $this->cod_matricula = $this->getQueryString(name: 'ref_cod_matricula');
        $this->ref_cod_aluno = $this->getQueryString(name: 'ref_cod_aluno');

        $this->validaPermissao();
        $this->validaParametros();

        return 'Editar';
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'cod_matricula', valor: $this->cod_matricula);
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        $this->nome_url_cancelar = 'Voltar';
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        // Início > Escola > Alunos > Nome > Matrícula > Tipo do AEE
        $registration = LegacyRegistration::find($this->cod_matricula);
        $this->breadcrumb(currentPage: 'Tipo do AEE', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
            url(path: 'intranet/educar_aluno_lst.php') => 'Alunos',
            url(path: "intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url(path: "intranet/educar_matricula_det.php?cod_matricula={$this->cod_matricula}") => 'Matrícula',
        ]);

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

        $enturmacoes = $this->getEnturmacoesAee();

        foreach ($enturmacoes as $enturmacao) {
            $tipoAtendimento = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $enturmacao['tipo_atendimento']));

            $helperOptions = ['objectName' => "{$enturmacao['ref_cod_turma']}_{$enturmacao['sequencial']}_tipoatendimento"];
            $options = [
                'label' => "Tipo de atendimento educacional especializado do aluno na turma {$enturmacao->schoolClass->nm_turma}: ",
                'options' => [
                    'values' => $tipoAtendimento,
                    'all_values' => TipoAtendimentoAluno::getDescriptiveValues(),
                ],
                'required' => false,
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);
        }
    }

    public function Editar()
    {
        $this->validaPermissao();
        $this->validaParametros();

        $enturmacoes = $this->getEnturmacoesAee();

        $arrayTipoAtendimento = [];
        foreach ($enturmacoes as $enturmacao) {
            $arrayTipoAtendimento[] = [
                'value' => request(key: $enturmacao['ref_cod_turma'] . '_' . $enturmacao['sequencial'] . '_tipoatendimento'),
                'turma' => $enturmacao['ref_cod_turma'],
                'sequencial' => $enturmacao['sequencial'],
            ];
        }

        foreach ($arrayTipoAtendimento as $data) {
            $tipoAtendimento = $data['value'] ? implode(separator: ',', array: $data['value']) : null;

            app(EnrollmentService::class)->updateFromScreen($this->cod_matricula, $data['turma'], $data['sequencial'], [
                'ref_usuario_exc' => $this->pessoa_logada,
                'tipo_atendimento' => $tipoAtendimento,
            ]);
        }

        $this->mensagem = 'Tipo do AEE do aluno atualizado com sucesso.<br>';
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
                url: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}",
                registro: 'matrícula',
                feminino: true,
                codigo: $this->cod_matricula,
                dica: 'Acesse a matrícula pela ficha do aluno.',
            );
        }
    }

    private function getEnturmacoesAee()
    {
        return LegacyEnrollment::query()
            ->when(is_numeric($this->cod_matricula), fn ($q) => $q->whereRegistration($this->cod_matricula))
            ->whereHas('registration', function ($q) {
                $q->whereHas('student', fn ($q) => $q->active());
            })
            ->whereHas('schoolClass', function ($q) {
                $q->whereRaw('? = ANY(tipo_atendimento)', [TipoAtendimentoTurma::AEE]);
            })
            ->with('schoolClass:cod_turma,nm_turma')
            ->get([
                'ref_cod_turma',
                'sequencial',
                'tipo_atendimento',
            ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo do AEE do aluno';
        $this->processoAp = '578';
    }
};
