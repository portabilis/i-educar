<?php

use App\Models\LegacyEnrollment;
use App\Models\RegistrationStatus;
use App\Process;
use App\Services\EnrollmentService;
use Carbon\Carbon;

return new class extends clsCadastro
{
    public $ref_cod_matricula;

    public $ref_cod_turma;

    public $sequencial;

    public $matricula_situacao;

    public function Inicializar()
    {
        $retorno = 'Editar';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->sequencial = $_GET['sequencial'];

        if ($this->user()->cannot(abilities: 'modify', arguments: Process::ENROLLMENT_HISTORY)) {
            $this->simpleRedirect(url: "/enrollment-history/{$this->ref_cod_matricula}");
        }

        $this->fexcluir = $this->user()->can(abilities: 'remove', arguments: Process::ENROLLMENT_HISTORY);

        $this->breadcrumb(currentPage: 'Histórico de enturmações da matrícula', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
        $this->url_cancelar = route(name: 'enrollments.enrollment-history', parameters: ['id' => $this->ref_cod_matricula]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);
        $this->campoOculto(nome: 'ref_cod_turma', valor: $this->ref_cod_turma);

        $enturmacao = $this->buscarEnturmacao();

        $matricula = $enturmacao->registration;
        $escola = $matricula->school;
        $instituicao = $escola->institution;

        $this->campoRotulo(nome: 'ano', campo: 'Ano', valor: $matricula->ano);
        $this->campoRotulo(nome: 'nm_instituicao', campo: 'Instituição', valor: $instituicao->nm_instituicao);
        $this->campoRotulo(nome: 'nm_escola', campo: 'Escola', valor: $escola->name);
        $this->campoRotulo(nome: 'nm_pessoa', campo: 'Nome do Aluno', valor: $enturmacao->studentName);
        $this->campoRotulo(nome: 'sequencial', campo: 'Sequencial', valor: $enturmacao->sequencial);

        $situacao = RegistrationStatus::getRegistrationAndEnrollmentStatus()[$matricula->aprovado] ?? '';

        $required = !$enturmacao->ativo;

        $this->campoRotulo(nome: 'situacao', campo: 'Situação', valor: $situacao);
        $this->inputsHelper()->date(attrName: 'data_enturmacao', inputOptions: ['label' => 'Data enturmação', 'value' => $enturmacao->data_enturmacao?->format('d/m/Y'), 'placeholder' => '']);
        $this->inputsHelper()->date(attrName: 'data_exclusao', inputOptions: ['label' => 'Data de saída', 'value' => $enturmacao->data_exclusao?->format('d/m/Y'), 'placeholder' => '', 'required' => $required]);

        $situacoesMatricula = [
            '' => 'Selecione',
            'transferido' => 'Transferido',
            'remanejado' => 'Remanejado',
            'reclassificado' => 'Reclassificado',
            'abandono' => 'Deixou de Frequentar',
            'falecido' => 'Falecido',
        ];

        $options = [
            'label' => 'Situação',
            'value' => $this->buscaSituacao(enturmacao: $enturmacao),
            'resources' => $situacoesMatricula,
            'inline' => true,
            'required' => false,
        ];

        $this->inputsHelper()->select(attrName: 'matricula_situacao', inputOptions: $options);

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: '/vendor/legacy/intranet/scripts/extra/matricua-historico.js');
    }

    public function buscaSituacao(LegacyEnrollment $enturmacao): string
    {
        foreach (['transferido', 'remanejado', 'reclassificado', 'abandono', 'falecido'] as $situacao) {
            if ($enturmacao->{$situacao}) {
                return $situacao;
            }
        }

        return '';
    }

    public function Editar()
    {
        $enturmacao = $this->buscarEnturmacao();

        $dataEnturmacao = dataToBanco(data_original: $this->data_enturmacao);
        $dataExclusao = dataToBanco(data_original: $this->data_exclusao);

        $erroData = $this->validarDatas($dataEnturmacao, $dataExclusao);

        if ($erroData) {
            $this->mensagem = $erroData;

            return false;
        }

        $matricula = $enturmacao->registration;
        $dataSaidaMatricula = $matricula->data_cancel
            ? $matricula->data_cancel->format('Y-m-d')
            : '';

        $seqUltimaEnturmacao = $this->ultimaEnturmacao();

        if (
            $dataSaidaMatricula
            && ($dataExclusao > $dataSaidaMatricula)
            && (
                $matricula->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO
                || $matricula->aprovado == App_Model_MatriculaSituacao::ABANDONO
                || $matricula->aprovado == App_Model_MatriculaSituacao::RECLASSIFICADO
            ) && ($this->sequencial == $seqUltimaEnturmacao)
        ) {
            $this->mensagem = 'Edição não realizada. A data de saída não pode ser posterior a data de saída da matricula.';

            return false;
        }

        if ($this->matricula_situacao && !$dataExclusao) {
            $this->mensagem = 'Edição não realizada. É necessário informar a data de saída ao selecionar uma situação.';

            return false;
        }

        if ($enturmacao->data_enturmacao?->format('Y-m-d') !== $dataEnturmacao) {
            $enturmacao->data_enturmacao = $dataEnturmacao;
            $enturmacao->save();
        }

        $enturmacaoService = new EnrollmentService(auth()->user());

        if ($this->matricula_situacao && $dataExclusao && $enturmacao->ativo) {
            $dataSaida = Carbon::parse($dataExclusao);

            $enturmacaoService->cancelEnrollment($enturmacao, $dataSaida);
        } else {
            $enturmacao->data_exclusao = $dataExclusao ?: null;
            $enturmacao->ref_usuario_exc = $this->pessoa_logada;
            $enturmacao->save();
        }

        if ($this->matricula_situacao) {
            $enturmacaoService->markWithSituation($enturmacao, $this->matricula_situacao);
        }

        if (empty($dataSaidaMatricula)) {
            $matricula->data_cancel = $dataExclusao;
            $matricula->save();
        }

        $this->mensagem = 'Edição efetuada com sucesso.';
        $this->simpleRedirect(url: "/enrollment-history/{$this->ref_cod_matricula}");
    }

    private function validarDatas(string $dataEnturmacao, ?string $dataExclusao): ?string
    {
        if ($dataExclusao && ($dataExclusao < $dataEnturmacao)) {
            return 'Edição não realizada. A data de saída não pode ser anterior a data de enturmação.';
        }

        $dataEntradaEnturmacaoSeguinte = $this->dataEntradaEnturmacaoSeguinte();

        if ($dataExclusao && $dataEntradaEnturmacaoSeguinte && ($dataExclusao > $dataEntradaEnturmacaoSeguinte)) {
            return 'Edição não realizada. A data de saída não pode ser posterior a data de entrada da enturmação seguinte.';
        }

        $dataSaidaEnturmacaoAnterior = $this->dataSaidaEnturmacaoAnterior();

        if ($dataSaidaEnturmacaoAnterior && ($dataEnturmacao < $dataSaidaEnturmacaoAnterior)) {
            return 'Edição não realizada. A data de enturmação não pode ser anterior a data de saída da enturmação antecessora.';
        }

        return null;
    }

    private function buscarEnturmacao(): LegacyEnrollment
    {
        return LegacyEnrollment::query()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->whereSchoolClass($this->ref_cod_turma)
            ->where('sequencial', $this->sequencial)
            ->firstOrFail();
    }

    private function dataSaidaEnturmacaoAnterior(): ?string
    {
        return LegacyEnrollment::query()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('sequencial', '<', $this->sequencial)
            ->orderByDesc('sequencial')
            ->value('data_exclusao');
    }

    private function dataEntradaEnturmacaoSeguinte(): ?string
    {
        return LegacyEnrollment::query()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('sequencial', '>', $this->sequencial)
            ->orderBy('sequencial')
            ->value('data_enturmacao');
    }

    private function ultimaEnturmacao(): ?int
    {
        return LegacyEnrollment::query()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->join('relatorio.view_situacao', function ($join) {
                $join->on('view_situacao.cod_matricula', 'matricula_turma.ref_cod_matricula')
                    ->on('view_situacao.cod_turma', 'matricula_turma.ref_cod_turma')
                    ->on('view_situacao.sequencial', 'matricula_turma.sequencial');
            })
            ->max('matricula_turma.sequencial');
    }

    private function enturmacaoRemanejadaMesmaTurma($sequencial)
    {
        return LegacyEnrollment::query()
            ->where('ref_cod_turma', $this->ref_cod_turma)
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('sequencial', $sequencial)
            ->where('remanejado_mesma_turma', true)
            ->first();
    }

    public function Excluir()
    {
        $enturmacao = $this->buscarEnturmacao();

        DB::beginTransaction();

        if ($enturmacao->remanejado_mesma_turma) {
            $proximaEnturmacao = $this->enturmacaoRemanejadaMesmaTurma($this->sequencial + 1);

            if ($proximaEnturmacao) {
                $proximaEnturmacao->update(['remanejado_mesma_turma' => false]);
            }
        }

        $excluiu = $enturmacao->delete();
        DB::commit();

        if ($excluiu) {
            $this->mensagem = 'Exclusão efetuada com sucesso.';
            $this->simpleRedirect(url: "/enrollment-history/{$this->ref_cod_matricula}");
        }

        $this->mensagem = 'Exclusão não realizada.';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Bloqueio do ano letivo';

        $this->processoAp = Process::ENROLLMENT_HISTORY;
    }
};
