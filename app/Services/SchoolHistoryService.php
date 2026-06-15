<?php

namespace App\Services;

use App\Models\LegacyComplementSchool;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App_Model_IedFinder;
use Illuminate\Support\Facades\DB;

class SchoolHistoryService
{
    public function __construct(
        private GlobalAverageService $globalAverageService
    ) {}

    public function gerarHistoricoTransferencia(int $registrationId, int $userId): void
    {
        $dadosMatricula = $this->dadosMatricula($registrationId);

        if (!$this->deveGerarHistorico($dadosMatricula['ref_cod_instituicao'])) {
            return;
        }

        $dadosEscola = $this->dadosEscola($dadosMatricula['ref_ref_cod_escola']);
        $gradeCursoId = str_contains($dadosMatricula['nome_curso'], '8') ? 1 : 2;

        DB::transaction(function () use ($dadosMatricula, $userId, $dadosEscola, $gradeCursoId, $registrationId) {
            $sequencial = $this->getNextSequencial($dadosMatricula['ref_cod_aluno']);

            $historicoEscolar = LegacySchoolHistory::create([
                'ref_cod_aluno' => $dadosMatricula['ref_cod_aluno'],
                'sequencial' => $sequencial,
                'ref_usuario_cad' => $userId,
                'nm_serie' => $dadosMatricula['nome_serie'],
                'ano' => $dadosMatricula['ano'],
                'carga_horaria' => $dadosMatricula['carga_horaria'],
                'escola' => $dadosEscola['nome'],
                'escola_cidade' => $dadosEscola['cidade'],
                'escola_uf' => $dadosEscola['uf'],
                'aprovado' => 4,
                'ativo' => 1,
                'ref_cod_instituicao' => $dadosMatricula['ref_cod_instituicao'],
                'ref_cod_matricula' => $registrationId,
                'nm_curso' => $dadosMatricula['nome_curso'],
                'historico_grade_curso_id' => $gradeCursoId,
                'ref_cod_escola' => $dadosMatricula['ref_ref_cod_escola'],
            ]);

            $disciplinas = $this->getDisciplineNames($dadosMatricula['cod_serie'], $dadosMatricula['ref_ref_cod_escola'], $dadosMatricula['ano']);
            foreach ($disciplinas as $index => $nome) {
                LegacySchoolHistoryDiscipline::create([
                    'historico_escolar_id' => $historicoEscolar->id,
                    'sequencial' => $index + 1,
                    'ref_ref_cod_aluno' => $dadosMatricula['ref_cod_aluno'],
                    'ref_sequencial' => $historicoEscolar->sequencial,
                    'nm_disciplina' => $nome,
                    'nota' => '',
                ]);
            }
        });
    }

    public function insertGlobalAverage(int $studentId, int $historySequencial, int $disciplineSequencial): void
    {
        $historicoEscolar = LegacySchoolHistory::forStudentSequential($studentId, $historySequencial)->first();

        if (!$historicoEscolar || !$historicoEscolar->ref_cod_matricula) {
            return;
        }

        $registration = LegacyRegistration::findOrFail($historicoEscolar->ref_cod_matricula);
        $average = $this->globalAverageService->getGlobalAverage($registration);
        $mediaGeral = $this->roundGrade($registration->cod_matricula, $average);
        $mediaGeral = is_numeric($mediaGeral) ? number_format($mediaGeral, 1, '.', ',') : $mediaGeral;

        LegacySchoolHistoryDiscipline::updateOrCreate([
            'sequencial' => $disciplineSequencial,
            'ref_ref_cod_aluno' => $studentId,
            'ref_sequencial' => $historySequencial,
        ], [
            'nm_disciplina' => 'Média Geral',
            'nota' => $mediaGeral,
            'historico_escolar_id' => $historicoEscolar->id,
        ]);
    }

    public function getSchoolName(int $schoolId): ?string
    {
        $school = LegacySchool::find($schoolId);
        $name = $school?->name;

        if (!$name) {
            $name = LegacyComplementSchool::where('ref_cod_escola', $schoolId)->value('nm_escola');
        }

        return $name ?: null;
    }

    public function getNextSequencial(int $studentId): int
    {
        return (LegacySchoolHistory::forStudent($studentId)->max('sequencial') ?? 0) + 1;
    }

    private function deveGerarHistorico(int $institutionId): bool
    {
        return dbBool(
            DB::selectOne('SELECT gerar_historico_transferencia FROM pmieducar.instituicao WHERE cod_instituicao = ?', [$institutionId])
                ?->gerar_historico_transferencia
        );
    }

    private function dadosMatricula(int $registrationId): array
    {
        return (array) DB::selectOne('
            SELECT m.ref_cod_aluno, nm_serie AS nome_serie, s.cod_serie, m.ano,
                   m.ref_ref_cod_escola, c.ref_cod_instituicao, c.nm_curso AS nome_curso, s.carga_horaria
            FROM pmieducar.matricula m
            INNER JOIN pmieducar.serie s ON m.ref_ref_cod_serie = s.cod_serie
            INNER JOIN pmieducar.curso c ON m.ref_cod_curso = c.cod_curso
            WHERE m.cod_matricula = ?
        ', [$registrationId]);
    }

    private function dadosEscola(int $schoolId): array
    {
        return (array) DB::selectOne('
            SELECT nome, municipio AS cidade, uf_municipio AS uf
            FROM relatorio.view_dados_escola
            WHERE cod_escola = ?
        ', [$schoolId]);
    }

    private function getDisciplineNames(int $gradeId, int $schoolId, int $year): array
    {
        $results = DB::select("
            SELECT translate(upper(cc.nome),
                'áéíóúýàèìòùãõâêîôûäëïöüÿçÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ',
                'AEIOUYAEIOUAOAEIOUAEIOUYCAEIOUYAEIOUAOAEIOUAEIOUC') AS nome
            FROM pmieducar.escola_serie_disciplina esd
            INNER JOIN modules.componente_curricular cc ON (esd.ref_cod_disciplina = cc.id)
            WHERE esd.ref_ref_cod_serie = ?
              AND esd.ref_ref_cod_escola = ?
              AND ? = ANY(esd.anos_letivos)
        ", [$gradeId, $schoolId, $year]);

        return array_column($results, 'nome');
    }

    private function roundGrade(int $registrationId, $grade)
    {
        $regraAvaliacao = App_Model_IedFinder::getRegraAvaliacaoPorMatricula($registrationId);

        return $regraAvaliacao->tabelaArredondamento->round($grade, 2);
    }
}
