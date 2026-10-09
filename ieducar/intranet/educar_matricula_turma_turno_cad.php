<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Services\EnrollmentService;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;

return new class extends clsCadastro
{
    public $cod_matricula;

    public $ref_cod_aluno;

    public $turno;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 689);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            // Inalcançável: o `simpleRedirect()` acima interrompe a execução.
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }
    }

    public function Formular()
    {
        $this->title = 'Turno do aluno';
        $this->processoAp = '578';
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
        $this->campoOculto(nome: 'cod_matricula', valor: $this->cod_matricula);
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        // Início > Escola > Alunos > Nome > Matrícula > Turno
        $registration = LegacyRegistration::find($this->cod_matricula);
        $this->breadcrumb(currentPage: 'Turno', breadcrumbs: [
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
        $enturmacoes = LegacyEnrollment::active()
            ->with([
                'schoolClass:cod_turma,ref_cod_curso,nm_turma,turma_turno_id',
                'schoolClass.course:cod_curso,modalidade_curso',
            ])
            ->where('ref_cod_matricula', $this->cod_matricula)
            ->get([
                'ref_cod_turma',
                'sequencial',
                'turno_id',
            ]);

        $turnos = [
            0 => 'Selecione',
            clsPmieducarTurma::TURNO_MATUTINO => 'Matutino',
            clsPmieducarTurma::TURNO_VESPERTINO => 'Vespertino',
        ];

        foreach ($enturmacoes as $enturmacao) {
            if ($enturmacao->schoolClass->turma_turno_id != clsPmieducarTurma::TURNO_INTEGRAL) {
                continue;
            }

            if ($enturmacao->schoolClass->course->modalidade_curso === ModalidadeCurso::EJA) {
                $turnos[clsPmieducarTurma::TURNO_NOTURNO] = 'Noturno';
            }

            $this->campoLista(nome: "turno[{$enturmacao->ref_cod_turma}-{$enturmacao->sequencial}]", campo: "Turno do aluno na turma: {$enturmacao->schoolClass->name}", valor: $turnos, default: $enturmacao->turno_id, descricao: 'Não é necessário preencher o campo quando o aluno cursar o turno INTEGRAL', obrigatorio: false);
        }

        $this->acao_enviar = 'showConfirmationMessage(this)';
    }

    public function Editar()
    {
        $this->validaPermissao();
        $this->validaParametros();

        $is_change = false;
        foreach ($this->turno as $codTurmaESequencial => $turno) {
            // Necessário pois chave é Turma + Matrícula + Sequencial
            $codTurmaESequencial = explode(separator: '-', string: $codTurmaESequencial);
            $codTurma = $codTurmaESequencial[0];
            $sequencial = $codTurmaESequencial[1];

            $turnoEnturmacao = LegacyEnrollment::query()
                ->where('ref_cod_matricula', $this->cod_matricula)
                ->where('ref_cod_turma', $codTurma)
                ->where('sequencial', $sequencial)
                ->value('turno_id');

            if ($turnoEnturmacao != (int) $turno) {
                $is_change = true;

                app(EnrollmentService::class)->updateFromScreen($this->cod_matricula, $codTurma, $sequencial, [
                    'ref_usuario_exc' => $this->pessoa_logada,
                    'turno_id' => $turno,
                ]);
            }
        }

        session()->flash(key: 'success', value: $is_change ? 'Turno alterado com sucesso!' : 'Não houve alteração no valor do campo Turno.');

        $this->simpleRedirect(url('intranet/educar_matricula_det.php?cod_matricula='.$this->cod_matricula));
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-matricula-turma-turno.js');
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
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
};
