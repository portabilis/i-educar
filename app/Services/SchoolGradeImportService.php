<?php

namespace App\Services;

use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SchoolGradeImportService
{
    private const STATUS_COMPLETED = 'completed';

    private const STATUS_FAILED = 'failed';

    private Collection $schools;

    private Collection $grades;

    private Collection $schoolAcademicYears;

    private Collection $schoolCourses;

    private Collection $gradeDisciplines;

    public function processSchoolGradeUpdate(array $params): array
    {
        $validationResult = $this->validateParams($params);
        if (!$validationResult['valid']) {
            return $this->createFailureResult(0, 0, [['error' => $validationResult['message']]], $validationResult['message']);
        }

        $total = $this->calculateTotal($params);
        $this->loadCollections($params);

        $validationResult = $this->validateAll($params);
        if (!empty($validationResult['errors'])) {
            return $this->createFailureResult($total, 0, $validationResult['errors'], 'Atualização falhou devido a erros encontrados.');
        }

        return $this->processValidData($validationResult['validatedData'], $params, $total);
    }

    private function calculateTotal(array $params): int
    {
        if (!empty($params['escola_serie_data'])) {
            return collect($params['escola_serie_data'])
                ->sum(fn ($line) => count($line['escolas'] ?? []) * count($line['series'] ?? []));
        }

        return count($params['schools']) * count($params['grades']);
    }

    private function validateParams(array $params): array
    {
        if (empty($params['schools']) || empty($params['grades']) || empty($params['year'])) {
            return $this->createValidationResult(false, 'Parâmetros de escola, série e ano letivo são obrigatórios.');
        }

        if (!is_array($params['schools']) || !is_array($params['grades'])) {
            return $this->createValidationResult(false, 'Parâmetros de escola e série devem ser arrays.');
        }

        if (!is_numeric($params['year']) || (int) $params['year'] != $params['year']) {
            return $this->createValidationResult(false, 'Ano letivo deve ser um número inteiro.');
        }

        return $this->createValidationResult(true, 'Parâmetros válidos.');
    }

    private function createValidationResult(bool $valid, string $message): array
    {
        return [
            'valid' => $valid,
            'message' => $message,
        ];
    }

    private function createFailureResult(int $total, int $processed, array $errors, string $message): array
    {
        return [
            'status' => self::STATUS_FAILED,
            'total' => $total,
            'processed' => $processed,
            'errors' => $errors,
            'details' => [],
            'message' => $message,
        ];
    }

    private function loadCollections(array $params): void
    {
        $this->schools = LegacySchool::whereIn('cod_escola', $params['schools'])
            ->where('ativo', 1)
            ->get()
            ->keyBy('cod_escola');

        $this->grades = LegacyGrade::whereIn('cod_serie', $params['grades'])
            ->where('ativo', 1)
            ->select('cod_serie', 'nm_serie', 'ref_cod_curso')
            ->get()
            ->keyBy('cod_serie');

        $this->schoolAcademicYears = LegacySchoolAcademicYear::whereIn('ref_cod_escola', $params['schools'])
            ->where('ano', $params['year'])
            ->where('ativo', 1)
            ->select('ref_cod_escola', 'andamento')
            ->get()
            ->keyBy('ref_cod_escola');

        $this->schoolCourses = LegacySchoolCourse::whereIn('ref_cod_escola', $params['schools'])
            ->whereRaw('? = ANY(anos_letivos)', [$params['year']])
            ->where('ativo', 1)
            ->select('ref_cod_escola', 'ref_cod_curso')
            ->get()
            ->groupBy('ref_cod_escola');

        $this->gradeDisciplines = LegacyDisciplineAcademicYear::whereIn('ano_escolar_id', $params['grades'])
            ->whereYearEq($params['year'])
            ->select('ano_escolar_id', 'componente_curricular_id')
            ->get()
            ->groupBy('ano_escolar_id');
    }

    private function validateAll(array $params): array
    {
        $validatedData = [];
        $errors = [];

        if (!empty($params['escola_serie_data'])) {
            foreach ($params['escola_serie_data'] as $lineNumber => $line) {
                $schools = $line['escolas'] ?? [];
                $grades = $line['series'] ?? [];

                $lineValidation = $this->validateSchoolGradeCombinations($schools, $grades, $params['year']);
                $validatedData = array_merge($validatedData, $lineValidation['validatedData']);
                $errors = array_merge($errors, $lineValidation['errors']);
            }
        } else {
            $lineValidation = $this->validateSchoolGradeCombinations($params['schools'], $params['grades'], $params['year']);
            $validatedData = $lineValidation['validatedData'];
            $errors = $lineValidation['errors'];
        }

        return [
            'validatedData' => $validatedData,
            'errors' => $errors,
        ];
    }

    private function validateSchoolGradeCombinations(array $schools, array $grades, int $year): array
    {
        $validatedData = [];
        $errors = [];

        foreach ($schools as $escolaId) {
            foreach ($grades as $serieId) {
                $validationResult = $this->validateSchoolGradeCombinationFromCollections(
                    $escolaId,
                    $serieId,
                    $year
                );

                if ($validationResult['success']) {
                    $validatedData[] = $validationResult['data'];
                } else {
                    $errors[] = [
                        'school_id' => $escolaId,
                        'grade_id' => $serieId,
                        'error' => $validationResult['error'],
                    ];
                }
            }
        }

        return [
            'validatedData' => $validatedData,
            'errors' => $errors,
        ];
    }

    private function processValidData(array $validatedData, array $params, int $total): array
    {
        $processed = 0;
        $errors = [];
        $details = [];

        DB::beginTransaction();

        try {
            foreach ($validatedData as $data) {
                $result = $this->processSchoolGrade($data['school'], $data['grade'], $params['year'], $params['user'], $params);
                $processed++;
                $details[] = [
                    'type' => 'success',
                    'message' => "Escola '{$data['school']->name}' e série '{$data['grade']->nm_serie}' {$result['action']} com sucesso.",
                    'school_id' => $data['school']->cod_escola,
                    'grade_id' => $data['grade']->cod_serie,
                ];
            }

            DB::commit();

            $message = "Processadas {$processed} escola/série com sucesso.";

            return [
                'status' => self::STATUS_COMPLETED,
                'total' => $total,
                'processed' => $processed,
                'errors' => $errors,
                'details' => $details,
                'message' => $message,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->createFailureResult(
                $total,
                $processed,
                [['school_id' => 0, 'grade_id' => 0, 'error' => 'Erro interno no processamento.']],
                'Erro crítico durante o processamento.'
            );
        }
    }

    private function updateAcademicYears(mixed $currentYears, int $newYear): array
    {
        if (is_string($currentYears)) {
            $currentYears = transformStringFromDBInArray($currentYears) ?? [];
        }

        if (!in_array($newYear, $currentYears)) {
            $currentYears[] = $newYear;
        }

        return $currentYears;
    }

    private function validateSchoolGradeCombinationFromCollections(
        int $escolaId,
        int $serieId,
        int $year
    ): array {
        try {
            $school = $this->schools->get($escolaId);

            if (!$school) {
                return [
                    'success' => false,
                    'error' => "Escola ID {$escolaId} não encontrada ou inativa.",
                ];
            }

            $grade = $this->grades->get($serieId);

            if (!$grade) {
                return [
                    'success' => false,
                    'error' => "Série ID {$serieId} não encontrada ou inativa.",
                ];
            }

            $schoolAcademicYear = $this->schoolAcademicYears->get($school->cod_escola);

            if (!$schoolAcademicYear) {
                return [
                    'success' => false,
                    'error' => "Escola '{$school->name}' não possui ano letivo {$year} cadastrado.",
                ];
            }

            if ($schoolAcademicYear->andamento != LegacySchoolAcademicYear::IN_PROGRESS) {
                $statusText = match ($schoolAcademicYear->andamento) {
                    LegacySchoolAcademicYear::NOT_INITIALIZED => 'não iniciado',
                    LegacySchoolAcademicYear::FINALIZED => 'finalizado',
                    default => 'desconhecido'
                };

                return [
                    'success' => false,
                    'error' => "Escola '{$school->name}' possui ano letivo {$year} {$statusText}. O ano letivo deve estar em andamento.",
                ];
            }

            $schoolCourse = $this->schoolCourses->get($school->cod_escola);

            if (!$schoolCourse) {
                return [
                    'success' => false,
                    'error' => "Escola '{$school->name}' não possui o ano {$year} cadastrado em nenhum curso.",
                ];
            }

            $schoolHasGradeCourse = $schoolCourse->where('ref_cod_curso', $grade->ref_cod_curso)->isNotEmpty();

            if (!$schoolHasGradeCourse) {
                return [
                    'success' => false,
                    'error' => "Escola '{$school->name}' não possui o curso da série '{$grade->nm_serie}' cadastrado para o ano {$year}.",
                ];
            }

            $gradeDisciplines = $this->gradeDisciplines->get($grade->cod_serie);

            if (!$gradeDisciplines || $gradeDisciplines->isEmpty()) {
                return [
                    'success' => false,
                    'error' => "Série '{$grade->nm_serie}' não possui componentes curriculares cadastrados para o ano {$year}. Não há componentes a serem copiados.",
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'school' => $school,
                    'grade' => $grade,
                    'schoolAcademicYear' => $schoolAcademicYear,
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Erro ao validar escola ID {$escolaId} e série ID {$serieId}.",
            ];
        }
    }

    private function processSchoolGrade($school, $grade, $academicYear, $user, $params): array
    {
        $existingSchoolGrade = LegacySchoolGrade::where('ref_cod_escola', $school->cod_escola)
            ->where('ref_cod_serie', $grade->cod_serie)
            ->first();

        $blockingParams = [
            'bloquear_enturmacao_sem_vagas' => $params['bloquear_enturmacao_sem_vagas'] ?? 0,
            'bloquear_cadastro_turma_para_serie_com_vagas' => $params['bloquear_cadastro_turma_para_serie_com_vagas'] ?? 0,
        ];

        if (!$existingSchoolGrade) {
            $schoolGrade = new LegacySchoolGrade;
            $schoolGrade->ref_cod_escola = $school->cod_escola;
            $schoolGrade->ref_cod_serie = $grade->cod_serie;
            $schoolGrade->ref_usuario_cad = $user->id;
            $schoolGrade->ativo = 1;
            $schoolGrade->anos_letivos = transformDBArrayInString([$academicYear]);
            $schoolGrade->data_cadastro = now();
            $schoolGrade->bloquear_enturmacao_sem_vagas = $blockingParams['bloquear_enturmacao_sem_vagas'];
            $schoolGrade->bloquear_cadastro_turma_para_serie_com_vagas = $blockingParams['bloquear_cadastro_turma_para_serie_com_vagas'];
            $schoolGrade->save();

            $action = 'criada';
        } else {
            $anosLetivos = $existingSchoolGrade->anos_letivos ?? [];
            $anosLetivos = $this->updateAcademicYears($anosLetivos, $academicYear);

            $existingSchoolGrade->ativo = 1; // Reativar
            $existingSchoolGrade->anos_letivos = transformDBArrayInString($anosLetivos);
            $existingSchoolGrade->bloquear_enturmacao_sem_vagas = $blockingParams['bloquear_enturmacao_sem_vagas'];
            $existingSchoolGrade->bloquear_cadastro_turma_para_serie_com_vagas = $blockingParams['bloquear_cadastro_turma_para_serie_com_vagas'];
            $existingSchoolGrade->save();

            $action = 'atualizada';
        }

        $existingDisciplines = LegacySchoolGradeDiscipline::where('ref_ref_cod_serie', $grade->cod_serie)
            ->where('ref_ref_cod_escola', $school->cod_escola)
            ->where('ativo', 1)
            ->get()
            ->keyBy('ref_cod_disciplina');

        $disciplines = LegacyDisciplineAcademicYear::query()
            ->whereGrade($grade->cod_serie)
            ->whereYearEq($academicYear)
            ->with('discipline')
            ->get();

        foreach ($disciplines as $discipline) {
            $existingDiscipline = $existingDisciplines->get($discipline->componente_curricular_id);

            if (!$existingDiscipline) {
                $schoolGradeDiscipline = new LegacySchoolGradeDiscipline;
                $schoolGradeDiscipline->ref_ref_cod_serie = $grade->cod_serie;
                $schoolGradeDiscipline->ref_ref_cod_escola = $school->cod_escola;
                $schoolGradeDiscipline->ref_cod_disciplina = $discipline->componente_curricular_id;
                $schoolGradeDiscipline->ativo = 1;
                $schoolGradeDiscipline->anos_letivos = transformDBArrayInString([$academicYear]);
                $schoolGradeDiscipline->save();
            } else {
                $anosLetivos = $existingDiscipline->anos_letivos ?? [];

                $anosLetivos = $this->updateAcademicYears($anosLetivos, $academicYear);

                $existingDiscipline->anos_letivos = transformDBArrayInString($anosLetivos);
                $existingDiscipline->save();
            }
        }

        return [
            'action' => $action,
            'school' => $school,
            'grade' => $grade,
        ];
    }
}
