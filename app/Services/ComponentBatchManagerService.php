<?php

namespace App\Services;

use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\NotificationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ComponentBatchManagerService
{
    private const PREVIEW_KEY_TO_TABLE = [
        'nota_media' => 'modules.nota_componente_curricular_media',
        'nota' => 'modules.nota_componente_curricular',
        'falta' => 'modules.falta_componente_curricular',
        'parecer' => 'modules.parecer_componente_curricular',
        'componente_turma' => 'modules.componente_curricular_turma',
        'professor_disciplina' => 'modules.professor_turma_disciplina',
        'escola_serie_disciplina' => 'pmieducar.escola_serie_disciplina',
        'componente_ano_escolar' => 'modules.componente_curricular_ano_escolar',
        'dispensa' => 'pmieducar.dispensa_disciplina',
    ];

    private const RECORD_MODELS = [
        'nota_media' => [LegacyDisciplineScoreAverage::class, 'registrationScore'],
        'nota' => [LegacyDisciplineScore::class, 'registrationScore'],
        'falta' => [LegacyDisciplineAbsence::class, 'studentAbsence'],
        'parecer' => [LegacyDisciplineDescriptiveOpinion::class, 'studentDescriptiveOpinion'],
    ];

    private const TABLE_PKS = [
        'modules.nota_componente_curricular' => ['nota_aluno_id', 'componente_curricular_id', 'etapa'],
        'modules.nota_componente_curricular_media' => ['nota_aluno_id', 'componente_curricular_id'],
        'modules.falta_componente_curricular' => ['falta_aluno_id', 'componente_curricular_id', 'etapa'],
        'modules.parecer_componente_curricular' => ['parecer_aluno_id', 'componente_curricular_id', 'etapa'],
        'modules.componente_curricular_turma' => ['componente_curricular_id', 'turma_id'],
        'modules.professor_turma_disciplina' => ['professor_turma_id', 'componente_curricular_id'],
        'pmieducar.escola_serie_disciplina' => ['id'],
        'modules.componente_curricular_ano_escolar' => ['componente_curricular_id', 'ano_escolar_id'],
        'pmieducar.dispensa_disciplina' => ['cod_dispensa'],
        'pmieducar.dispensa_etapa' => ['ref_cod_dispensa', 'etapa'],
    ];

    private const ESCOLA_SERIE_UNIQUE = ['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'];

    private const AUTO_INCREMENT_COLUMNS = [
        'pmieducar.escola_serie_disciplina' => 'id',
    ];

    private const EXCLUIDOS_TABLES = [
        'modules.componente_curricular_turma' => [
            'excluidos' => 'modules.componente_curricular_turma_excluidos',
            'match_columns' => ['componente_curricular_id', 'turma_id'],
        ],
        'pmieducar.escola_serie_disciplina' => [
            'excluidos' => 'pmieducar.escola_serie_disciplina_excluidos',
            'match_columns' => ['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'],
        ],
        'modules.componente_curricular_ano_escolar' => [
            'excluidos' => 'modules.componente_curricular_ano_escolar_excluidos',
            'match_columns' => ['componente_curricular_id', 'ano_escolar_id'],
        ],
        'pmieducar.dispensa_disciplina' => [
            'excluidos' => 'pmieducar.dispensa_disciplina_excluidos',
            'match_columns' => ['cod_dispensa'],
        ],
    ];

    public const TABLE_LABELS = [
        'modules.nota_componente_curricular' => 'Notas',
        'modules.nota_componente_curricular_media' => 'Médias',
        'modules.falta_componente_curricular' => 'Faltas',
        'modules.parecer_componente_curricular' => 'Pareceres',
        'modules.componente_curricular_turma' => 'Componentes da turma',
        'modules.professor_turma_disciplina' => 'Disciplinas do vínculo professor/turma',
        'pmieducar.escola_serie_disciplina' => 'Componentes da série da escola',
        'modules.componente_curricular_ano_escolar' => 'Componentes da série',
        'pmieducar.dispensa_disciplina' => 'Dispensas',
    ];

    private const INSERT_ORDER = [
        'modules.componente_curricular_ano_escolar',
        'pmieducar.escola_serie_disciplina',
        'modules.componente_curricular_turma',
        'modules.professor_turma_disciplina',
        'pmieducar.dispensa_disciplina',
        'pmieducar.dispensa_etapa',
        'modules.nota_componente_curricular',
        'modules.falta_componente_curricular',
        'modules.parecer_componente_curricular',
        'modules.nota_componente_curricular_media',
    ];

    public function calculatePreview(array $params): array
    {
        $year = $params['year'];
        $gradeIds = $params['grade_ids'];
        $schoolIds = $params['school_ids'] ?? null;
        $disciplineIds = $params['discipline_ids'];
        $removeRecords = $params['remove_records'] ?? false;
        $unlinkClassComponents = $params['unlink_class_components'] ?? false;
        $unlinkTeacherDisciplines = $params['unlink_teacher_disciplines'] ?? false;
        $unlinkSchoolGradeDisciplines = $params['unlink_school_grade_disciplines'] ?? false;
        $unlinkGradeComponents = $params['unlink_grade_components'] ?? false;

        $turmaIds = $this->getAffectedTurmaIds(year: $year, gradeIds: $gradeIds, schoolIds: $schoolIds);

        $preview = [
            'turma_count' => count($turmaIds),
            'idiario' => null,
        ];

        if ($removeRecords && !($params['skip_idiario'] ?? false) && iDiarioService::hasIdiarioConfigurations()) {
            $preview['idiario'] = $this->getIdiarioPreview($params);
        }

        if ($removeRecords) {
            foreach (self::RECORD_MODELS as $key => [$model, $relation]) {
                $preview[$key] = $this->registrationScopedQuery($model, $relation, $gradeIds, $disciplineIds, $year, $schoolIds)->count();
            }
        }

        if ($unlinkClassComponents && count($turmaIds) > 0) {
            $preview['componente_turma'] = $this->componentesTurmaQuery($turmaIds, $disciplineIds)->count();
        }

        if ($unlinkTeacherDisciplines && count($turmaIds) > 0) {
            $preview['professor_disciplina'] = $this->professorDisciplinaQuery($turmaIds, $disciplineIds)->count();
            $preview['professor_turma'] = $this->professorTurmaQuery($turmaIds)->count();
        }

        if ($unlinkSchoolGradeDisciplines) {
            $safeCount = $this->escolaSerieDisciplinaQuery($gradeIds, $disciplineIds, $schoolIds, $year)
                ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                ->count();
            $totalCount = $this->escolaSerieDisciplinaQuery($gradeIds, $disciplineIds, $schoolIds)
                ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                ->count();

            $preview['escola_serie_disciplina'] = $safeCount;

            if ($totalCount > $safeCount) {
                $preview['escola_serie_disciplina_skipped'] = $totalCount - $safeCount;
            }
        }

        if ($unlinkGradeComponents) {
            $safeCount = $this->componenteAnoEscolarQuery($gradeIds, $disciplineIds, $year, $schoolIds)
                ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                ->count();
            $totalCount = $this->componenteAnoEscolarQuery($gradeIds, $disciplineIds)
                ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                ->count();

            $preview['componente_ano_escolar'] = $safeCount;

            if ($totalCount > $safeCount) {
                $preview['componente_ano_escolar_skipped'] = $totalCount - $safeCount;
            }
        }

        if ($params['remove_exemptions'] ?? false) {
            $preview['dispensa'] = $this->dispensaQuery($gradeIds, $disciplineIds, $year, $schoolIds)->count();
        }

        return $preview;
    }

    public function getProtectionDetails(array $params, array $preview): array
    {
        $year = $params['year'];
        $gradeIds = $params['grade_ids'];
        $schoolIds = $params['school_ids'] ?? null;
        $disciplineIds = $params['discipline_ids'];

        $details = [];

        if (($preview['componente_ano_escolar_skipped'] ?? 0) > 0) {
            $blocked = $this->componenteAnoEscolarQuery($gradeIds, $disciplineIds)
                ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                ->whereExists(function ($sub) use ($year, $schoolIds) {
                    $sub->selectRaw('1')
                        ->from('pmieducar.escola_serie_disciplina as esd')
                        ->whereColumn('esd.ref_cod_disciplina', 'componente_curricular_ano_escolar.componente_curricular_id')
                        ->whereColumn('esd.ref_ref_cod_serie', 'componente_curricular_ano_escolar.ano_escolar_id')
                        ->whereRaw('ARRAY[?::smallint] <@ esd.anos_letivos', [$year])
                        ->when($schoolIds, fn ($q) => $q->whereNotIn('esd.ref_ref_cod_escola', $schoolIds));
                })
                ->with(['discipline', 'grade'])
                ->get();

            $items = [];

            foreach ($blocked as $record) {
                $blockingSchools = LegacySchoolGradeDiscipline::query()
                    ->where('ref_cod_disciplina', $record->componente_curricular_id)
                    ->where('ref_ref_cod_serie', $record->ano_escolar_id)
                    ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                    ->when($schoolIds, fn ($q) => $q->whereNotIn('ref_ref_cod_escola', $schoolIds))
                    ->with('school')
                    ->get()
                    ->map(fn ($esd) => $esd->school->name ?? "Escola {$esd->ref_ref_cod_escola}")
                    ->toArray();

                $items[] = [
                    'componente' => $record->discipline->name ?? "ID {$record->componente_curricular_id}",
                    'serie' => $record->grade->nm_serie ?? "ID {$record->ano_escolar_id}",
                    'escolas' => $blockingSchools,
                ];
            }

            if ($items) {
                $details['componente_ano_escolar'] = $items;
            }
        }

        if (($preview['escola_serie_disciplina_skipped'] ?? 0) > 0) {
            $blocked = $this->escolaSerieDisciplinaQuery($gradeIds, $disciplineIds, $schoolIds)
                ->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year])
                ->whereRaw('array_length(anos_letivos, 1) = 1')
                ->whereExists(function ($sub) use ($year) {
                    $sub->selectRaw('1')
                        ->from('modules.componente_curricular_turma as cct')
                        ->join('pmieducar.turma as t', 't.cod_turma', 'cct.turma_id')
                        ->whereColumn('cct.componente_curricular_id', 'escola_serie_disciplina.ref_cod_disciplina')
                        ->whereColumn('cct.escola_id', 'escola_serie_disciplina.ref_ref_cod_escola')
                        ->whereColumn('cct.ano_escolar_id', 'escola_serie_disciplina.ref_ref_cod_serie')
                        ->where('t.ano', '!=', $year);
                })
                ->with(['discipline', 'school', 'grade'])
                ->get();

            $items = [];

            foreach ($blocked as $record) {
                $blockingYears = DB::table('modules.componente_curricular_turma as cct')
                    ->join('pmieducar.turma as t', 't.cod_turma', 'cct.turma_id')
                    ->where('cct.componente_curricular_id', $record->ref_cod_disciplina)
                    ->where('cct.escola_id', $record->ref_ref_cod_escola)
                    ->where('cct.ano_escolar_id', $record->ref_ref_cod_serie)
                    ->where('t.ano', '!=', $year)
                    ->distinct()
                    ->pluck('t.ano')
                    ->sort()
                    ->values()
                    ->toArray();

                $items[] = [
                    'componente' => $record->discipline->name ?? "ID {$record->ref_cod_disciplina}",
                    'escola' => $record->school->name ?? "Escola {$record->ref_ref_cod_escola}",
                    'serie' => $record->grade->nm_serie ?? "ID {$record->ref_ref_cod_serie}",
                    'anos_bloqueando' => $blockingYears,
                ];
            }

            if ($items) {
                $details['escola_serie_disciplina'] = $items;
            }
        }

        return $details;
    }

    public static function sumPreviewCounts(array $preview): array
    {
        $totalIeducar = 0;
        foreach ($preview as $key => $value) {
            if (is_int($value) && $key !== 'turma_count' && $key !== 'professor_turma' && !str_ends_with($key, '_skipped')) {
                $totalIeducar += $value;
            }
        }

        $totalIdiario = self::sumIdiarioCounts($preview['idiario'] ?? null);

        return ['totalIeducar' => $totalIeducar, 'totalIdiario' => $totalIdiario];
    }

    public static function sumIdiarioCounts(?array $idiarioData): int
    {
        $total = 0;

        if ($idiarioData && !isset($idiarioData['error'])) {
            foreach ($idiarioData as $item) {
                if (is_array($item) && isset($item['count'])) {
                    $total += (int) $item['count'];
                }
            }
        }

        return $total;
    }

    public function execute(ComponentBatchOperation $operation): array
    {
        $operation->update(['status_id' => ComponentBatchStatus::RUNNING->value]);

        $data = $operation->data;
        $previewCounts = $data['preview_counts'] ?? [];

        ['totalIeducar' => $totalIeducar, 'totalIdiario' => $totalIdiario] = self::sumPreviewCounts($previewCounts);

        $warnings = [];
        $needsIdiario = $totalIdiario > 0 && ($data['remove_records'] ?? false) && iDiarioService::hasIdiarioConfigurations();

        if ($needsIdiario) {
            $result = app(iDiarioService::class)->deleteDisciplineRecords(
                array_merge($data, ['operation_id' => $operation->id])
            );

            // iDiário vai processar na fila e chamar o webhook com o resultado
            if (!empty($result['queued'])) {
                $data['awaiting_callback'] = true;
                $operation->update(['data' => $data]);

                return [];
            }

            // Fallback: resposta síncrona (iDiário antigo sem fila)
            $warnings = $this->processIdiarioResult($result, $data, $previewCounts, $operation);
        }

        return $this->completeExecution($operation, $data, $totalIeducar, $warnings);
    }

    public function processCallback(ComponentBatchOperation $operation, array $idiarioResult): void
    {
        $failed = false;

        try {
            $this->handleIdiarioCallback($operation, $idiarioResult);
        } catch (\Throwable $e) {
            $this->failed($operation, $e->getMessage());
            $failed = true;
        }

        $text = $failed
            ? 'Erro na remoção de componentes. Clique para ver detalhes.'
            : 'Remoção de componentes concluída com sucesso.';

        (new NotificationService)->createByUser(
            userId: $operation->user_id,
            text: $text,
            link: route('component-batch-manager.show', $operation),
            type: NotificationType::OTHER
        );
    }

    public function handleIdiarioCallback(ComponentBatchOperation $operation, array $idiarioResult): void
    {
        $data = $operation->data;
        $previewCounts = $data['preview_counts'] ?? [];

        ['totalIeducar' => $totalIeducar] = self::sumPreviewCounts($previewCounts);

        $warnings = $this->processIdiarioResult($idiarioResult, $data, $previewCounts, $operation);

        $this->completeExecution($operation, $data, $totalIeducar, $warnings);
    }

    private function completeExecution(
        ComponentBatchOperation $operation,
        array &$data,
        int $totalIeducar,
        array $warnings,
    ): array {
        $backup = [];

        if ($totalIeducar > 0) {
            [$counts, $backup] = $this->executeIeducarDeletion($data);
        } else {
            $counts = [];
        }

        $data['result_counts'] = $counts;

        $postCounts = $this->calculatePreview(array_merge($data, ['skip_idiario' => true]));
        $data['post_counts'] = $postCounts;

        $warnings = array_merge($warnings, $this->buildVerificationWarnings($postCounts));
        $data['verification_warnings'] = $warnings;

        $operation->update([
            'status_id' => ComponentBatchStatus::COMPLETED->value,
            'data' => $data,
            'backup' => $backup ?: null,
        ]);

        return $counts;
    }

    public function failed(ComponentBatchOperation $operation, string $error): void
    {
        $operation->refresh();

        $friendlyMessage = str_starts_with($error, 'Exclusão do i-Diário')
            || str_starts_with($error, 'Não foi possível')
            ? $error
            : 'Ocorreu um erro inesperado durante a execução. Contate o administrador.';

        $data = $operation->data ?? [];
        $data['error_detail'] = $error;

        $operation->update([
            'status_id' => ComponentBatchStatus::FAILED->value,
            'error_message' => $friendlyMessage,
            'data' => $data,
        ]);
    }

    private function processIdiarioResult(array $result, array &$data, array $previewCounts, ComponentBatchOperation $operation): array
    {
        $warnings = [];

        if (($result['success'] ?? false) !== true) {
            $data['idiario_error_detail'] = $result;
            $operation->update(['data' => $data]);

            throw new \RuntimeException('Não foi possível excluir os registros do i-Diário. Verifique se o serviço está disponível e tente novamente.');
        }

        $data['idiario_deleted'] = $result['deleted'] ?? null;

        $data['post_idiario'] = $this->getIdiarioPreview($data);
        $remainingIdiario = self::sumIdiarioCounts($data['post_idiario']);

        if ($remainingIdiario > 0) {
            $operation->update(['data' => $data]);

            throw new \RuntimeException(
                "Exclusão do i-Diário incompleta: {$remainingIdiario} registros remanescentes. Operação no i-Educar cancelada."
            );
        }

        $previewIdiario = self::sumIdiarioCounts($previewCounts['idiario'] ?? null);
        if ($data['idiario_deleted'] !== null && $data['idiario_deleted'] !== $previewIdiario) {
            $warnings[] = "i-Diário: destroy_batch retornou {$data['idiario_deleted']} exclusões, mas o preview contava {$previewIdiario}. Recontagem confirmou 0 remanescentes.";
        }

        return $warnings;
    }

    private function executeIeducarDeletion(array $data): array
    {
        $year = $data['year'];
        $gradeIds = $data['grade_ids'];
        $schoolIds = $data['school_ids'] ?? null;
        $disciplineIds = $data['discipline_ids'];

        return DB::transaction(function () use ($data, $year, $gradeIds, $schoolIds, $disciplineIds) {
            $counts = [];
            $backup = [];

            if ($data['remove_records'] ?? false) {
                foreach (self::RECORD_MODELS as $key => [$model, $relation]) {
                    $query = $this->registrationScopedQuery($model, $relation, $gradeIds, $disciplineIds, $year, $schoolIds);
                    $counts[$key] = $this->snapshotDeleteAndBackup($query, (new $model)->getTable(), $backup);
                }
            }

            $turmaIds = (($data['unlink_class_components'] ?? false) || ($data['unlink_teacher_disciplines'] ?? false))
                ? $this->getAffectedTurmaIds(year: $year, gradeIds: $gradeIds, schoolIds: $schoolIds)
                : [];

            if (($data['unlink_class_components'] ?? false) && count($turmaIds) > 0) {
                $query = $this->componentesTurmaQuery($turmaIds, $disciplineIds);
                $counts['componente_turma'] = $this->snapshotDeleteAndBackup($query, (new LegacyDisciplineSchoolClass)->getTable(), $backup);
            }

            if (($data['unlink_teacher_disciplines'] ?? false) && count($turmaIds) > 0) {
                $query = $this->professorDisciplinaQuery($turmaIds, $disciplineIds);
                $counts['professor_disciplina'] = $this->snapshotDeleteAndBackup($query, (new LegacySchoolClassTeacherDiscipline)->getTable(), $backup);
                $counts['professor_turma'] = $this->professorTurmaQuery($turmaIds)->update(['updated_at' => now()]);
                $backup['touched']['modules.professor_turma'] = ['turma_ids' => $turmaIds];
            }

            if ($data['unlink_school_grade_disciplines'] ?? false) {
                $base = $this->escolaSerieDisciplinaQuery($gradeIds, $disciplineIds, $schoolIds, $year);
                $counts['escola_serie_disciplina'] = $this->unlinkByRemovingYear($base, 'pmieducar.escola_serie_disciplina', $year, $backup, uniqueOverride: self::ESCOLA_SERIE_UNIQUE);
            }

            if ($data['unlink_grade_components'] ?? false) {
                $base = $this->componenteAnoEscolarQuery($gradeIds, $disciplineIds, $year);
                $counts['componente_ano_escolar'] = $this->unlinkByRemovingYear($base, 'modules.componente_curricular_ano_escolar', $year, $backup);
            }

            if ($data['remove_exemptions'] ?? false) {
                $query = $this->dispensaQuery($gradeIds, $disciplineIds, $year, $schoolIds);
                $table = (new LegacyDisciplineExemption)->getTable();
                $rows = $this->snapshotRows($query);

                if ($rows) {
                    $dispensaIds = collect($rows)->pluck('cod_dispensa')->toArray();

                    // Deletar etapas primeiro (FK sem CASCADE) — padrão issues #6653/#6824
                    $etapaRows = DB::table('pmieducar.dispensa_etapa')
                        ->whereIn('ref_cod_dispensa', $dispensaIds)
                        ->get()->map(fn ($r) => (array) $r)->toArray();

                    if ($etapaRows) {
                        $backup['deleted']['pmieducar.dispensa_etapa'] = [
                            'pk' => ['ref_cod_dispensa', 'etapa'],
                            'rows' => $etapaRows,
                        ];
                    }

                    DB::table('pmieducar.dispensa_etapa')
                        ->whereIn('ref_cod_dispensa', $dispensaIds)
                        ->delete();

                    // Depois deletar as dispensas (trigger copia para dispensa_disciplina_excluidos)
                    $backup['deleted'][$table] = ['pk' => self::TABLE_PKS[$table], 'rows' => $rows];

                    $counts['dispensa'] = DB::table($table)
                        ->whereIn('cod_dispensa', $dispensaIds)
                        ->delete();
                } else {
                    $counts['dispensa'] = 0;
                }
            }

            return [$counts, $backup];
        });
    }

    private function snapshotDeleteAndBackup(Builder $query, string $table, array &$backup): int
    {
        $rows = $this->snapshotRows($query);

        if ($rows) {
            $backup['deleted'][$table] = ['pk' => self::TABLE_PKS[$table], 'rows' => $rows];
        }

        return $query->delete();
    }

    private function unlinkByRemovingYear(Builder $baseQuery, string $table, int $year, array &$backup, ?array $uniqueOverride = null): int
    {
        $pkCols = self::TABLE_PKS[$table];
        $withYear = fn (Builder $q) => (clone $q)->whereRaw('ARRAY[?::smallint] <@ anos_letivos', [$year]);

        $allRows = $this->snapshotRows($withYear($baseQuery), intArrayColumns: ['anos_letivos']);

        $updated = $withYear($baseQuery)->update([
            'anos_letivos' => DB::raw("array_remove(anos_letivos, {$year}::smallint)"),
            'updated_at' => now(),
        ]);

        if (empty($allRows)) {
            return $updated;
        }

        $keyExtractor = $this->buildKeyExtractor($pkCols);

        $deletedKeys = collect($allRows)
            ->filter(fn ($row) => empty(array_diff($row['anos_letivos'] ?? [], [$year])))
            ->map($keyExtractor)
            ->toArray();

        [$deletedRows, $updatedRows] = collect($allRows)
            ->partition(fn ($r) => in_array($keyExtractor($r), $deletedKeys));

        $meta = ['pk' => $pkCols];
        if ($uniqueOverride) {
            $meta['unique'] = $uniqueOverride;
        }

        if ($deletedRows->isNotEmpty()) {
            $deletedRowsArray = $deletedRows->values()->toArray();
            $backup['deleted'][$table] = $meta + ['rows' => $deletedRowsArray];
            $this->deleteByColumns($table, $pkCols, $deletedRowsArray);
        }

        if ($updatedRows->isNotEmpty()) {
            $backup['updated'][$table] = $meta + ['rows' => $updatedRows->values()->toArray()];
        }

        return $updated;
    }

    private function buildVerificationWarnings(array $postCounts): array
    {
        $warnings = [];
        ['totalIeducar' => $remainingIeducar] = self::sumPreviewCounts($postCounts);

        if ($remainingIeducar > 0) {
            foreach ($postCounts as $key => $value) {
                if (is_int($value) && $key !== 'turma_count' && $key !== 'professor_turma' && !str_ends_with($key, '_skipped') && $value > 0) {
                    $table = self::PREVIEW_KEY_TO_TABLE[$key] ?? null;
                    $label = $table ? (self::TABLE_LABELS[$table] ?? $key) : $key;
                    $warnings[] = "{$label}: {$value} registros remanescentes após exclusão.";
                }
            }
        }

        return $warnings;
    }

    public function restore(ComponentBatchOperation $operation, bool $forceBackup = false): array
    {
        $backup = $operation->backup;

        if (empty($backup)) {
            throw new \RuntimeException('Operação não possui backup para restaurar.');
        }

        $summary = [];

        DB::transaction(function () use ($backup, $forceBackup, &$summary) {
            foreach ($backup['updated'] ?? [] as $table => $meta) {
                $count = $this->restoreUpdatedTable($table, $meta, $forceBackup);
                $summary[$table] = ['action' => 'atualizados', 'count' => $count];
            }

            $deleted = $backup['deleted'] ?? [];

            foreach (self::INSERT_ORDER as $table) {
                if (!isset($deleted[$table])) {
                    continue;
                }

                $count = $this->restoreDeletedTable($table, $deleted[$table], $forceBackup);
                $summary[$table] = ['action' => 'reinseridos', 'count' => $count];
            }
        });

        $operation->update(['status_id' => ComponentBatchStatus::RESTORED->value]);

        return $summary;
    }

    private function restoreDeletedTable(string $table, array $meta, bool $forceBackup): int
    {
        $rows = $meta['rows'] ?? [];

        if (empty($rows)) {
            return 0;
        }

        if (isset(self::EXCLUIDOS_TABLES[$table])) {
            $config = self::EXCLUIDOS_TABLES[$table];
            $this->deleteByColumns($config['excluidos'], $config['match_columns'], $rows);
        }

        $rows = $this->prepareRowsForInsert($table, $rows);
        $conflictCols = $meta['unique'] ?? $meta['pk'] ?? self::TABLE_PKS[$table] ?? [];
        $columns = array_keys($rows[0]);

        foreach (array_chunk($rows, 500) as $chunk) {
            [$sql, $bindings] = $this->buildUpsertSql($table, $columns, $conflictCols, $chunk, $forceBackup);
            DB::statement($sql, $bindings);
        }

        // Retorna total do backup (pode ser maior que o real se houver conflitos)
        return count($meta['rows']);
    }

    private function prepareRowsForInsert(string $table, array $rows): array
    {
        $autoCol = self::AUTO_INCREMENT_COLUMNS[$table] ?? null;

        return array_map(function ($row) use ($autoCol) {
            if ($autoCol) {
                unset($row[$autoCol]);
            }

            if (isset($row['anos_letivos']) && is_array($row['anos_letivos'])) {
                $row['anos_letivos'] = $this->formatPgSmallintArray($row['anos_letivos']);
            }

            return $row;
        }, $rows);
    }

    private function buildUpsertSql(string $table, array $columns, array $conflictCols, array $chunk, bool $forceBackup): array
    {
        $bindings = [];
        $placeholders = [];

        foreach ($chunk as $row) {
            $rowPlaceholders = [];
            foreach ($columns as $col) {
                $value = $row[$col] ?? null;
                if ($col === 'anos_letivos' && is_string($value) && str_starts_with($value, "'{")) {
                    $rowPlaceholders[] = $value . '::smallint[]';
                } else {
                    $rowPlaceholders[] = '?';
                    $bindings[] = $value;
                }
            }
            $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }

        $colList = '"' . implode('", "', $columns) . '"';
        $conflictList = '"' . implode('", "', $conflictCols) . '"';
        $valuesClause = implode(', ', $placeholders);

        $onConflict = $this->buildOnConflictClause($columns, $conflictCols, $forceBackup);

        $sql = "INSERT INTO {$table} ({$colList}) VALUES {$valuesClause} ON CONFLICT ({$conflictList}) {$onConflict}";

        return [$sql, $bindings];
    }

    private function buildOnConflictClause(array $columns, array $conflictCols, bool $forceBackup): string
    {
        if (!$forceBackup) {
            return 'DO NOTHING';
        }

        $updateCols = array_diff($columns, $conflictCols);

        if (empty($updateCols)) {
            return 'DO NOTHING';
        }

        $setClauses = array_map(fn ($col) => "\"{$col}\" = EXCLUDED.\"{$col}\"", $updateCols);

        return 'DO UPDATE SET ' . implode(', ', $setClauses);
    }

    private function restoreUpdatedTable(string $table, array $meta, bool $forceBackup): int
    {
        $rows = $meta['rows'] ?? [];
        $pkCols = $meta['pk'] ?? self::TABLE_PKS[$table] ?? [];
        $count = 0;

        foreach ($rows as $row) {
            if (!isset($row['anos_letivos']) || !is_array($row['anos_letivos'])) {
                continue;
            }

            $originalValue = $this->formatPgSmallintArray($row['anos_letivos']);

            $query = DB::table($table);
            foreach ($pkCols as $col) {
                if (!isset($row[$col])) {
                    throw new \RuntimeException("Backup corrompido: coluna PK '{$col}' ausente na tabela {$table}.");
                }
                $query->where($col, $row[$col]);
            }

            $expression = $forceBackup
                ? "{$originalValue}::smallint[]"
                : "(SELECT ARRAY(SELECT DISTINCT unnest(COALESCE(anos_letivos, '{}') || {$originalValue}::smallint[]) ORDER BY 1))";

            $count += $query->update([
                'anos_letivos' => DB::raw($expression),
                'updated_at' => now(),
            ]);
        }

        return $count;
    }

    public function buildWarnings(array $params, array $preview): array
    {
        $warnings = [];

        if (($params['unlink_class_components'] ?? false)
            && !($params['remove_records'] ?? false)) {
            $hasRecords = ($preview['nota_media'] ?? 0) + ($preview['nota'] ?? 0)
                + ($preview['falta'] ?? 0) + ($preview['parecer'] ?? 0);

            if ($hasRecords === 0 && ($preview['turma_count'] ?? 0) > 0) {
                $tempPreview = $this->calculatePreview(array_merge($params, [
                    'remove_records' => true,
                    'unlink_class_components' => false,
                    'unlink_teacher_disciplines' => false,
                    'unlink_school_grade_disciplines' => false,
                    'unlink_grade_components' => false,
                    'skip_idiario' => true,
                ]));
                $hasRecords = ($tempPreview['nota_media'] ?? 0) + ($tempPreview['nota'] ?? 0)
                    + ($tempPreview['falta'] ?? 0) + ($tempPreview['parecer'] ?? 0);
            }

            if ($hasRecords > 0) {
                $warnings[] = "Existem {$hasRecords} lançamentos. Desvincular sem remover pode gerar inconsistência.";
            }
        }

        if (($params['unlink_class_components'] ?? false)
            && !($params['unlink_teacher_disciplines'] ?? false)) {
            $warnings[] = 'Vínculos de professor/disciplina continuarão apontando para componentes removidos da turma.';
        }

        if ((($params['unlink_school_grade_disciplines'] ?? false) || ($params['unlink_grade_components'] ?? false))
            && !($params['unlink_class_components'] ?? false)) {
            $warnings[] = 'Componentes continuarão vinculados nas turmas.';
        }

        return $warnings;
    }

    private function getAffectedTurmaIds(int $year, array $gradeIds, ?array $schoolIds): array
    {
        return LegacySchoolClass::query()
            ->leftJoin('pmieducar.turma_serie as ts', function ($join) {
                $join->on('ts.turma_id', '=', 'turma.cod_turma')
                    ->where('turma.multiseriada', '=', 1);
            })
            ->where('turma.ativo', 1)
            ->where('turma.ano', $year)
            ->when($schoolIds, fn ($q) => $q->whereIn('turma.ref_ref_cod_escola', $schoolIds))
            ->whereIn(DB::raw('coalesce(ts.serie_id, turma.ref_ref_cod_serie)'), $gradeIds)
            ->where(function ($q) {
                $q->where('turma.multiseriada', '!=', 1)
                    ->orWhereNotNull('ts.serie_id');
            })
            ->distinct()
            ->pluck('turma.cod_turma')
            ->toArray();
    }

    private function getIdiarioPreview(array $params): ?array
    {
        try {
            return app(iDiarioService::class)->getDisciplineRecordsCount($params);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function registrationScopedQuery(
        string $modelClass,
        string $parentRelation,
        array $gradeIds,
        array $disciplineIds,
        int $year,
        ?array $schoolIds,
    ): Builder {
        return $modelClass::query()
            ->whereIn('componente_curricular_id', $disciplineIds)
            ->whereHas("{$parentRelation}.registration", function ($q) use ($gradeIds, $year, $schoolIds) {
                $q->whereIn('ref_ref_cod_serie', $gradeIds)
                    ->where('ano', $year)
                    ->when($schoolIds, fn ($q) => $q->whereIn('ref_ref_cod_escola', $schoolIds));
            });
    }

    private function componentesTurmaQuery(array $turmaIds, array $disciplineIds): Builder
    {
        return LegacyDisciplineSchoolClass::query()
            ->whereIn('turma_id', $turmaIds)
            ->whereIn('componente_curricular_id', $disciplineIds);
    }

    private function professorDisciplinaQuery(array $turmaIds, array $disciplineIds): Builder
    {
        return LegacySchoolClassTeacherDiscipline::query()
            ->whereIn('componente_curricular_id', $disciplineIds)
            ->whereHas('schoolClassTeacher', fn ($q) => $q->whereIn('turma_id', $turmaIds));
    }

    private function professorTurmaQuery(array $turmaIds): Builder
    {
        return LegacySchoolClassTeacher::query()
            ->whereIn('turma_id', $turmaIds);
    }

    private function escolaSerieDisciplinaQuery(array $gradeIds, array $disciplineIds, ?array $schoolIds = null, ?int $year = null): Builder
    {
        $query = LegacySchoolGradeDiscipline::query()
            ->whereIn('ref_ref_cod_serie', $gradeIds)
            ->whereIn('ref_cod_disciplina', $disciplineIds)
            ->when($schoolIds, fn ($q) => $q->whereIn('ref_ref_cod_escola', $schoolIds));

        if ($year !== null) {
            $query->where(function ($q) use ($year) {
                $q->whereRaw('array_length(anos_letivos, 1) > 1')
                    ->orWhere(function ($q2) use ($year) {
                        $q2->whereRaw('array_length(anos_letivos, 1) = 1')
                            ->whereNotExists(function ($sub) use ($year) {
                                $sub->selectRaw('1')
                                    ->from('modules.componente_curricular_turma as cct')
                                    ->join('pmieducar.turma as t', 't.cod_turma', 'cct.turma_id')
                                    ->whereColumn('cct.componente_curricular_id', 'escola_serie_disciplina.ref_cod_disciplina')
                                    ->whereColumn('cct.escola_id', 'escola_serie_disciplina.ref_ref_cod_escola')
                                    ->whereColumn('cct.ano_escolar_id', 'escola_serie_disciplina.ref_ref_cod_serie')
                                    ->where('t.ano', '!=', $year);
                            });
                    });
            });
        }

        return $query;
    }

    private function dispensaQuery(array $gradeIds, array $disciplineIds, int $year, ?array $schoolIds): Builder
    {
        return LegacyDisciplineExemption::query()
            ->whereIn('ref_cod_disciplina', $disciplineIds)
            ->whereIn('ref_cod_serie', $gradeIds)
            ->when($schoolIds, fn ($q) => $q->whereIn('ref_cod_escola', $schoolIds))
            ->whereHas('registration', fn ($q) => $q->where('ano', $year));
    }

    private function componenteAnoEscolarQuery(array $gradeIds, array $disciplineIds, ?int $year = null, ?array $schoolIds = null): Builder
    {
        $query = LegacyDisciplineAcademicYear::query()
            ->whereIn('ano_escolar_id', $gradeIds)
            ->whereIn('componente_curricular_id', $disciplineIds);

        if ($year !== null) {
            $query->whereNotExists(function ($sub) use ($year, $schoolIds) {
                $sub->selectRaw('1')
                    ->from('pmieducar.escola_serie_disciplina as esd')
                    ->whereColumn('esd.ref_cod_disciplina', 'componente_curricular_ano_escolar.componente_curricular_id')
                    ->whereColumn('esd.ref_ref_cod_serie', 'componente_curricular_ano_escolar.ano_escolar_id')
                    ->whereRaw('ARRAY[?::smallint] <@ esd.anos_letivos', [$year])
                    ->when($schoolIds, fn ($q) => $q->whereNotIn('esd.ref_ref_cod_escola', $schoolIds));
            });
        }

        return $query;
    }

    private function snapshotRows(Builder $query, array $intArrayColumns = []): array
    {
        $rows = (clone $query)->toBase()->get()->map(fn ($r) => (array) $r)->values()->toArray();

        if ($intArrayColumns) {
            foreach ($rows as &$row) {
                foreach ($intArrayColumns as $col) {
                    if (isset($row[$col]) && is_string($row[$col])) {
                        $inner = trim($row[$col], '{}');
                        $row[$col] = $inner === '' ? [] : array_map('intval', explode(',', $inner));
                    }
                }
            }
        }

        return $rows;
    }

    private function deleteByColumns(string $table, array $columns, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        if (count($columns) === 1) {
            $col = $columns[0];
            $values = array_values(array_unique(array_column($rows, $col)));

            foreach (array_chunk($values, 500) as $chunk) {
                DB::table($table)->whereIn($col, $chunk)->delete();
            }

            return;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            $tuples = [];
            $bindings = [];

            foreach ($chunk as $row) {
                $placeholders = [];
                foreach ($columns as $col) {
                    $placeholders[] = '?';
                    $bindings[] = $row[$col] ?? null;
                }
                $tuples[] = '(' . implode(', ', $placeholders) . ')';
            }

            $colList = '"' . implode('", "', $columns) . '"';
            DB::statement(
                "DELETE FROM {$table} WHERE ({$colList}) IN (" . implode(', ', $tuples) . ')',
                $bindings
            );
        }
    }

    private function buildKeyExtractor(array $pkCols): \Closure
    {
        return fn (array $r) => implode(':', array_map(fn ($c) => $r[$c], $pkCols));
    }

    private function formatPgSmallintArray(array $values): string
    {
        return "'{" . implode(',', array_map('intval', $values)) . "}'";
    }
}
