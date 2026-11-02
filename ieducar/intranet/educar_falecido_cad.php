<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Services\EnrollmentService;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 686);

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
        $retorno = 'Novo';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        // Início > Escola > Alunos > Nome > Matrícula > Falecimento
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->breadcrumb(currentPage: 'Falecimento', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
            url('intranet/educar_aluno_lst.php') => 'Alunos',
            url("intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url("intranet/educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}") => 'Matrícula',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);
        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);

        if (is_numeric($this->ref_cod_aluno)) {
            $this->nm_aluno = LegacyStudent::query()
                ->active()
                ->whereStudent($this->ref_cod_aluno)
                ->joinPerson()
                ->value('pessoa.nome');
        }

        if ($this->nm_aluno) {
            $this->campoTexto(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno, tamanhovisivel: 30, tamanhomaximo: 255, evento: '', disabled: true);
        }

        $this->inputsHelper()->date(attrName: 'data_cancel', inputOptions: ['label' => 'Data do falecimento', 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y')]);

        $this->campoMemo(nome: 'observacao', campo: 'Observação', valor: $this->observacao, colunas: 60, linhas: 5);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $tamanhoObs = strlen($this->observacao);
        if ($tamanhoObs > 300) {
            $this->mensagem = 'O campo observação deve conter no máximo 300 caracteres.<br>';

            return false;
        }

        $obj_matricula = new clsPmieducarMatricula(
            cod_matricula: $this->ref_cod_matricula,
            ref_ref_cod_escola: null,
            ref_ref_cod_serie: null,
            ref_usuario_exc: $this->pessoa_logada,
            ref_usuario_cad: null,
            ref_cod_aluno: null,
            aprovado: 6
        );
        $obj_matricula->data_cancel = Portabilis_Date_Utils::brToPgSQL($this->data_cancel);

        $det_matricula = $obj_matricula->detalhe();

        if (is_null($det_matricula['data_matricula'])) {
            if (substr(string: $det_matricula['data_cadastro'], offset: 0, length: 10) > $obj_matricula->data_cancel) {
                $this->mensagem = 'Data de falecimento não pode ser inferior a data da matrícula.<br>';

                return false;
            }
        } else {
            if (substr(string: $det_matricula['data_matricula'], offset: 0, length: 10) > $obj_matricula->data_cancel) {
                $this->mensagem = 'Data de falecimento não pode ser inferior a data da matrícula.<br>';

                return false;
            }
        }

        if ($obj_matricula->edita()) {
            if ($obj_matricula->cadastraObservacaoFalecido($this->observacao)) {
                $enturmacoes = LegacyEnrollment::query()
                    ->join('pmieducar.matricula', 'matricula.cod_matricula', 'matricula_turma.ref_cod_matricula')
                    ->join('pmieducar.aluno', 'aluno.cod_aluno', 'matricula.ref_cod_aluno')
                    ->active()
                    ->where('aluno.ativo', 1)
                    ->when(is_numeric($this->ref_cod_matricula), fn ($q) => $q->whereRegistration($this->ref_cod_matricula))
                    ->get([
                        'matricula_turma.ref_cod_turma',
                        'matricula_turma.sequencial',
                    ]);

                $enturmacaoService = app(EnrollmentService::class);

                DB::beginTransaction();

                foreach ($enturmacoes as $enturmacao) {
                    $atualizou = $enturmacaoService->updateFromScreen($this->ref_cod_matricula, $enturmacao->ref_cod_turma, $enturmacao->sequencial, [
                        'ref_usuario_exc' => $this->pessoa_logada,
                        'ativo' => 0,
                    ]);

                    if (!$atualizou) {
                        // Débito de cobertura: trecho ainda sem teste.
                        // @codeCoverageIgnoreStart
                        DB::rollBack();
                        $this->mensagem = 'Não foi possível desativar as enturmações da matrícula.';

                        return false;
                        // @codeCoverageIgnoreEnd
                    }

                    $enturmacaoService->registerDeath($this->ref_cod_matricula, $enturmacao->sequencial, $this->data_cancel);
                }

                DB::commit();

                $notaAluno = (new Avaliacao_Model_NotaAlunoDataMapper)
                    ->findAll(columns: ['id'], where: ['matricula_id' => $obj_matricula->cod_matricula])[0] ?? null;

                if (!empty($notaAluno)) {
                    (new Avaliacao_Model_NotaComponenteMediaDataMapper)
                        ->updateSituation(notaAlunoId: $notaAluno->get('id'), situacao: App_Model_MatriculaSituacao::FALECIDO);
                }

                $this->mensagem .= 'Alteração realizado com sucesso.<br>';
                $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
            }

            // Inalcançável: `cadastraObservacaoFalecido()` só falha sem o código da matrícula, que o `edita()` acima já exigiu.
            // @codeCoverageIgnoreStart
            $this->mensagem = 'A alteração não pode ser salva.<br>';

            return false;
            // @codeCoverageIgnoreEnd
        }
        $this->mensagem = 'A alteração não pode ser realizado.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Falecimento';
        $this->processoAp = '578';
    }
};
