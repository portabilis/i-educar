<?php

namespace App\Services;

use App\Exceptions\AcademicYearServiceException;
use App\Models\EmployeeAllocation;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\LegacyStageType;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AcademicYearService
{
    /**
     * Valida se as datas das etapas do ano letivo não conflitam com outros anos letivos da escola.
     * Usado tanto na criação quanto na edição de anos letivos.
     */
    public function validateAcademicYearDates(
        array $startDates,
        array $endDates,
        int $year,
        int $schoolId
    ): void {
        $dateRanges = $this->prepareDateRanges($startDates, $endDates);

        foreach ($dateRanges as $range) {
            if ($this->hasDateConflict($range['start'], $range['end'], $year, $schoolId)) {
                throw new AcademicYearServiceException('A data informada não pode fazer parte do período configurado para outros anos letivos.');
            }
        }
    }

    /**
     * Valida datas do ano letivo para múltiplas escolas.
     */
    public function validateAcademicYearDatesForMultipleSchools(
        array $schoolIds,
        array $startDates,
        array $endDates,
        int $year
    ): void {
        foreach ($schoolIds as $schoolId) {
            $this->validateAcademicYearDates($startDates, $endDates, $year, $schoolId);
        }
    }

    /**
     * Valida se é possível reduzir o número de etapas do ano letivo.
     * Usado apenas na edição, pois verifica se há dados nas etapas que serão removidas.
     */
    public function validateAcademicYearModules(
        int $year,
        int $schoolId,
        int $stagesCount
    ): void {
        $existingStagesCount = $this->getExistingStagesCount($year, $schoolId);

        if ($stagesCount >= $existingStagesCount) {
            return;
        }

        $stagesToRemove = range($stagesCount + 1, $existingStagesCount);

        if ($this->hasDataInStages($stagesToRemove, $year, $schoolId)) {
            throw new AcademicYearServiceException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
        }

        if ($this->hasOnlineDiaryData($stagesToRemove, $schoolId, $year)) {
            throw new AcademicYearServiceException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.');
        }
    }

    /**
     * Valida módulos do ano letivo para múltiplas escolas.
     * Usado apenas na edição em lote.
     */
    public function validateAcademicYearModulesForMultipleSchools(
        array $schoolIds,
        int $year,
        int $stagesCount
    ): void {
        foreach ($schoolIds as $schoolId) {
            $this->validateAcademicYearModules($year, $schoolId, $stagesCount);
        }
    }

    /**
     * Valida se o número de etapas informado confere com o módulo selecionado.
     */
    public function validateStageCountWithModule(int $moduleId, int $stagesCount): void
    {
        $module = LegacyStageType::query()->find($moduleId);

        if (!$module) {
            throw new AcademicYearServiceException('Módulo não encontrado.');
        }

        if ($stagesCount !== $module->num_etapas) {
            throw new AcademicYearServiceException('Quantidade de etapas informadas não confere com a quantidade de etapas da etapa selecionada.');
        }
    }

    /**
     * Cria um ano letivo completo para uma escola, incluindo etapas e cópia de dados do ano anterior.
     * Usado na criação de anos letivos.
     */
    public function createAcademicYearForSchool(
        int $schoolId,
        int $year,
        array $startDates,
        array $endDates,
        array $schoolDays,
        int $moduleId,
        bool $copySchoolClasses = true,
        bool $copyTeacherData = true,
        bool $copyEmployeeData = false,
        ?int $userId = null
    ): LegacySchoolAcademicYear {
        $this->initializeCopyCounters($schoolId);

        $this->performDataCopy($schoolId, $year, $copySchoolClasses, $copyTeacherData, $copyEmployeeData, $userId);

        $academicYear = $this->createSchoolAcademicYear($schoolId, $year, $copyTeacherData, $copyEmployeeData, $userId);
        $this->createAcademicYearStages($academicYear, $startDates, $endDates, $schoolDays, $moduleId, $schoolId, $year);

        return $academicYear;
    }

    /**
     * Retorna as escolas que já possuem ano letivo ativo para o ano especificado.
     * Usado para identificar escolas que devem ser ignoradas em processamentos em lote.
     */
    public function getExistingAcademicYearSchools(array $schoolIds, int $year): array
    {
        return LegacySchoolAcademicYear::query()
            ->where('ativo', 1)
            ->whereIn('ref_cod_escola', $schoolIds)
            ->where('ano', $year)
            ->pluck('ref_cod_escola')
            ->toArray();
    }

    /**
     * Executa a cópia de configurações do ano anterior para o novo ano letivo.
     * Atualiza cursos, séries, disciplinas e regras de avaliação para incluir o novo ano.
     */
    public function executeCopyAcademicYears(int $year, int $schoolId): void
    {
        $previousYear = LegacySchoolAcademicYear::query()
            ->whereSchool($schoolId)
            ->where('ano', '<', $year)
            ->max('ano');

        if (!$previousYear) {
            return;
        }

        LegacySchoolCourse::query()
            ->where('ref_cod_escola', $schoolId)
            ->whereRaw('? = ANY(anos_letivos)', [$previousYear])
            ->whereRaw('NOT (? = ANY(anos_letivos))', [$year])
            ->update([
                'anos_letivos' => DB::raw("array_append(anos_letivos, {$year}::smallint)"),
            ]);

        LegacySchoolGrade::query()
            ->where('ref_cod_escola', $schoolId)
            ->whereRaw('? = ANY(anos_letivos)', [$previousYear])
            ->whereRaw('NOT (? = ANY(anos_letivos))', [$year])
            ->update([
                'anos_letivos' => DB::raw("array_append(anos_letivos, {$year}::smallint)"),
            ]);

        LegacySchoolGradeDiscipline::query()
            ->whereSchool($schoolId)
            ->whereRaw('? = ANY(anos_letivos)', [$previousYear])
            ->whereRaw('NOT (? = ANY(anos_letivos))', [$year])
            ->update([
                'anos_letivos' => DB::raw("array_append(anos_letivos, {$year}::smallint)"),
            ]);

        LegacyDisciplineAcademicYear::query()
            ->whereRaw('EXISTS(
                SELECT 1
                FROM pmieducar.escola_serie_disciplina
                WHERE ? = ANY(anos_letivos)
                AND ref_ref_cod_escola = ?
                AND escola_serie_disciplina.ref_cod_disciplina = componente_curricular_ano_escolar.componente_curricular_id
                AND escola_serie_disciplina.ref_ref_cod_serie = componente_curricular_ano_escolar.ano_escolar_id
            )', [$previousYear, $schoolId])
            ->whereRaw('NOT (? = ANY(anos_letivos))', [$year])
            ->update([
                'anos_letivos' => DB::raw("array_append(anos_letivos, {$year}::smallint)"),
            ]);

        $this->copyEvaluationRulesForNewYear($schoolId, $previousYear, $year);
    }

    /**
     * Processa a criação de anos letivos em lote para múltiplas escolas.
     * Valida parâmetros, escolas e executa a criação para cada escola válida.
     */
    public function processAcademicYearBatch(array $params): array
    {
        $validationResult = $this->validateBatchParams($params);
        if (!$validationResult['valid']) {
            return $this->createBatchFailureResult(0, 0, [['error' => $validationResult['message']]], $validationResult['message'], $params['year']);
        }

        $total = $this->calculateBatchTotal($params);
        $this->loadBatchCollections($params);

        $validationResult = $this->validateAllSchools($params);

        if (!empty($validationResult['errors'])) {
            $allErrors = array_merge($validationResult['errors'], $validationResult['skippedSchools']);

            return $this->createBatchFailureResult($total, 0, $allErrors, 'Processamento cancelado devido a erros de validação.', $params['year']);
        }

        return $this->processBatchValidData($validationResult['validatedData'], $validationResult['skippedSchools'], $params, $total);
    }

    /**
     * Cria anos letivos para múltiplas escolas simultaneamente.
     * Usado na criação em lote de anos letivos.
     */
    public function createAcademicYearForMultipleSchools(
        array $schoolIds,
        int $year,
        array $startDates,
        array $endDates,
        array $schoolDays,
        int $moduleId,
        bool $copySchoolClasses = true,
        bool $copyTeacherData = true,
        bool $copyEmployeeData = false,
        ?int $userId = null
    ): array {
        $processedSchools = [];
        $skippedSchools = [];

        $existingActiveSchools = $this->getExistingAcademicYearSchools($schoolIds, $year);

        foreach ($schoolIds as $schoolId) {
            if (in_array($schoolId, $existingActiveSchools)) {
                $skippedSchools[] = $schoolId;

                continue;
            }

            try {
                $this->createAcademicYearForSchool(
                    schoolId: $schoolId,
                    year: $year,
                    startDates: $startDates,
                    endDates: $endDates,
                    schoolDays: $schoolDays,
                    moduleId: $moduleId,
                    copySchoolClasses: $copySchoolClasses,
                    copyTeacherData: $copyTeacherData,
                    copyEmployeeData: $copyEmployeeData,
                    userId: $userId
                );

                $processedSchools[] = $schoolId;
            } catch (Exception $e) {
                throw new AcademicYearServiceException('Erro ao criar ano letivo para escola ' . $schoolId . ': ' . $e->getMessage());
            }
        }

        return [
            'processed' => $processedSchools,
            'skipped' => $skippedSchools,
        ];
    }

    /**
     * Atualiza as etapas de um ano letivo existente.
     * Usado na edição de anos letivos.
     */
    public function updateAcademicYearStages(
        int $schoolId,
        int $year,
        array $startDates,
        array $endDates,
        array $schoolDays,
        int $moduleId
    ): void {
        $schoolAcademicYear = $this->getSchoolAcademicYear($schoolId, $year);

        LegacyAcademicYearStage::query()
            ->where('ref_ref_cod_escola', $schoolId)
            ->where('ref_ano', $year)
            ->delete();

        $this->createAcademicYearStages($schoolAcademicYear, $startDates, $endDates, $schoolDays, $moduleId, $schoolId, $year);
        $this->activateAcademicYear($schoolId, $year);
    }

    /**
     * Exclui um ano letivo, marcando como inativo e removendo as etapas.
     */
    public function deleteAcademicYear(int $schoolId, int $year, int $userId): bool
    {
        LegacySchoolAcademicYear::query()->where([
            'ref_cod_escola' => $schoolId,
            'ano' => $year,
        ])->update([
            'ref_usuario_cad' => $userId,
            'andamento' => 2,
            'ativo' => 0,
        ]);

        LegacyAcademicYearStage::query()
            ->where('ref_ref_cod_escola', $schoolId)
            ->where('ref_ano', $year)
            ->delete();

        return true;
    }

    private function copySchoolClassesFromPreviousYear(
        int $schoolId,
        int $destinationYear,
        bool $copyTeacherData = true,
        ?int $userId = null
    ): void {
        $lastSchoolAcademicYear = $this->getLastAcademicYear($schoolId);

        if (!$lastSchoolAcademicYear) {
            return;
        }

        $schoolClasses = $this->getSchoolClasses($schoolId, $lastSchoolAcademicYear);

        foreach ($schoolClasses as $schoolClass) {
            $this->copySchoolClass(
                originSchoolClass: (array) $schoolClass,
                originYear: $lastSchoolAcademicYear,
                destinationYear: $destinationYear,
                copyTeacherData: $copyTeacherData,
                userId: $userId
            );
        }

        if ($copyTeacherData) {
            $this->copyEmployeeAllocations($schoolId, $destinationYear, true);
        }
    }

    private function copySchoolClass(
        array $originSchoolClass,
        int $originYear,
        int $destinationYear,
        bool $copyTeacherData = true,
        ?int $userId = null
    ): void {
        if ($this->schoolClassExists($originSchoolClass, $destinationYear)) {
            return;
        }

        $schoolClass = LegacySchoolClass::query()->find($originSchoolClass['cod_turma']);
        if (!$schoolClass) {
            return;
        }

        $destinationSchoolClass = $this->replicateSchoolClass($schoolClass, $destinationYear, $userId);
        $destinationSchoolClassId = $destinationSchoolClass->getKey();

        $this->copySchoolClassRelatedData(
            originSchoolClass: $originSchoolClass,
            destinationSchoolClassId: $destinationSchoolClassId,
            originYear: $originYear,
            destinationYear: $destinationYear,
            copyTeacherData: $copyTeacherData
        );
    }

    private function copyEmployeeAllocations(
        int $schoolId,
        int $destinationYear,
        bool $onlyTeachers = false
    ): void {
        $lastSchoolAcademicYear = $this->getLastAcademicYear($schoolId);

        if (!$lastSchoolAcademicYear) {
            return;
        }

        $employeeAllocations = $this->getEmployeeAllocations($schoolId, $lastSchoolAcademicYear, $onlyTeachers);
        $existingAllocations = $this->getExistingAllocations($schoolId, $destinationYear);

        $type = $onlyTeachers ? 'alocacoes_professores' : 'alocacoes_servidores';

        foreach ($employeeAllocations as $allocation) {
            if ($this->allocationExists($allocation, $existingAllocations)) {
                continue;
            }

            $this->replicateAllocation($allocation, $destinationYear, $type);
        }
    }

    private function copySchoolClassDisciplines(int $originSchoolClassId, int $destinationSchoolClassId): void
    {
        $disciplines = LegacyDisciplineSchoolClass::query()
            ->where('turma_id', $originSchoolClassId)
            ->get();

        $existingSchoolClassDisciplines = LegacyDisciplineSchoolClass::query()
            ->where('turma_id', $destinationSchoolClassId)
            ->pluck('componente_curricular_id')
            ->toArray();

        $disciplinesToCreate = $disciplines->filter(
            fn ($discipline) => !in_array($discipline->componente_curricular_id, $existingSchoolClassDisciplines)
        );

        $this->bulkCreateDisciplines($disciplinesToCreate, $destinationSchoolClassId);
    }

    private function copySchoolClassModules(
        int $originSchoolClassId,
        int $destinationSchoolClassId,
        int $originYear,
        int $destinationYear
    ): void {
        $schoolClassModules = LegacySchoolClassStage::query()
            ->where('ref_cod_turma', $originSchoolClassId)
            ->get();

        $existingSchoolClassModules = LegacySchoolClassStage::query()
            ->where('ref_cod_turma', $destinationSchoolClassId)
            ->pluck('sequencial')
            ->toArray();

        $modulesToCreate = $schoolClassModules->filter(
            fn ($module) => !in_array($module->sequencial, $existingSchoolClassModules)
        );

        $this->bulkCreateSchoolClassModules($modulesToCreate, $destinationSchoolClassId, $originYear, $destinationYear);
    }

    private function validateAcademicYearModuleData(array $data): bool
    {
        $requiredFields = [
            'ref_ano', 'ref_ref_cod_escola', 'sequencial', 'ref_cod_modulo',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || !is_numeric($data[$field])) {
                return false;
            }
        }

        if (isset($data['dias_letivos']) && $data['dias_letivos'] !== null && $data['dias_letivos'] !== '' && !is_numeric($data['dias_letivos'])) {
            return false;
        }

        return !empty($data['data_inicio']) && !empty($data['data_fim']);
    }

    private function getLastAcademicYear(int $schoolId): ?int
    {
        return LegacySchoolAcademicYear::query()
            ->whereSchool($schoolId)
            ->active()
            ->max('ano');
    }

    private function getSchoolClasses(int $schoolId, int $year): array
    {
        return LegacySchoolClass::query()
            ->whereSchool($schoolId)
            ->whereYearEq($year)
            ->active()
            ->get()
            ->toArray();
    }

    private function schoolClassExists(array $originClass, int $destinationYear): bool
    {
        return LegacySchoolClass::query()
            ->whereSchool($originClass['ref_ref_cod_escola'])
            ->where('nm_turma', $originClass['nm_turma'])
            ->where('ref_ref_cod_serie', $originClass['ref_ref_cod_serie'])
            ->whereYearEq($destinationYear)
            ->active()
            ->visible()
            ->exists();
    }

    private function replicateSchoolClass(LegacySchoolClass $schoolClass, int $destinationYear, ?int $userId): LegacySchoolClass
    {
        $replicatedSchoolClass = $schoolClass->replicate([
            'data_cadastro', 'updated_at', 'data_exclusao',
            'parecer_1_etapa', 'parecer_2_etapa', 'parecer_3_etapa', 'parecer_4_etapa',
        ])->fill([
            'ano' => $destinationYear,
            'ref_usuario_cad' => $userId,
            'ref_usuario_exc' => $userId,
        ]);

        $replicatedSchoolClass->save();

        $this->incrementCopyCounter($schoolClass->ref_ref_cod_escola, 'turmas');

        return $replicatedSchoolClass;
    }

    private function copySchoolClassRelatedData(
        array $originSchoolClass,
        int $destinationSchoolClassId,
        int $originYear,
        int $destinationYear,
        bool $copyTeacherData
    ): void {
        $this->copySchoolClassDisciplines($originSchoolClass['cod_turma'], $destinationSchoolClassId);
        $this->copySchoolClassModules($originSchoolClass['cod_turma'], $destinationSchoolClassId, $originYear, $destinationYear);

        if ($originSchoolClass['multiseriada'] === 1) {
            $this->createMultiGradeSchoolClass($originSchoolClass, $destinationSchoolClassId);
        }

        if ($copyTeacherData) {
            $this->copySchoolClassTeachers(
                $originSchoolClass['cod_turma'],
                $destinationSchoolClassId,
                $originYear,
                $destinationYear,
                $originSchoolClass['ref_ref_cod_escola']
            );
        }
    }

    private function copySchoolClassTeachers(int $originClassId, int $destinationClassId, int $originYear, int $destinationYear, int $schoolId): void
    {
        $schoolClassTeachers = LegacySchoolClassTeacher::query()
            ->where(['ano' => $originYear, 'turma_id' => $originClassId])
            ->get();

        $existingSchoolClassTeachers = LegacySchoolClassTeacher::query()
            ->where(['ano' => $destinationYear, 'turma_id' => $destinationClassId])
            ->pluck('servidor_id')
            ->toArray();

        foreach ($schoolClassTeachers as $schoolClassTeacher) {
            if (in_array($schoolClassTeacher->servidor_id, $existingSchoolClassTeachers)) {
                continue;
            }

            $newSchoolClassTeacher = $schoolClassTeacher->replicate();
            $newSchoolClassTeacher->ano = $destinationYear;
            $newSchoolClassTeacher->turma_id = $destinationClassId;
            $newSchoolClassTeacher->data_inicial = null;
            $newSchoolClassTeacher->data_fim = null;
            $newSchoolClassTeacher->save();

            $this->incrementCopyCounter($schoolId, 'vinculos_professores');

            $this->copySchoolClassTeacherDisciplines($schoolClassTeacher, $newSchoolClassTeacher);
        }
    }

    private function copySchoolClassTeacherDisciplines(
        LegacySchoolClassTeacher $originSchoolClassTeacher,
        LegacySchoolClassTeacher $destinationSchoolClassTeacher
    ): void {
        $teacherDisciplines = LegacySchoolClassTeacherDiscipline::query()
            ->where('professor_turma_id', $originSchoolClassTeacher->getKey())
            ->get();

        $existingTeacherDisciplines = LegacySchoolClassTeacherDiscipline::query()
            ->where('professor_turma_id', $destinationSchoolClassTeacher->getKey())
            ->pluck('componente_curricular_id')
            ->toArray();

        foreach ($teacherDisciplines as $discipline) {
            if (in_array($discipline->componente_curricular_id, $existingTeacherDisciplines)) {
                continue;
            }

            $newDiscipline = $discipline->replicate();
            $newDiscipline->professor_turma_id = $destinationSchoolClassTeacher->getKey();
            $newDiscipline->save();
        }
    }

    private function getEmployeeAllocations(int $schoolId, int $year, bool $onlyTeachers): Collection
    {
        return EmployeeAllocation::query()
            ->whereHas('employee', fn ($q) => $q->active()->professor($onlyTeachers))
            ->whereSchool($schoolId)
            ->whereYearEq($year)
            ->active()
            ->get();
    }

    private function getExistingAllocations(int $schoolId, int $year): array
    {
        return EmployeeAllocation::query()
            ->whereHas('employee', fn ($q) => $q->active())
            ->whereSchool($schoolId)
            ->whereYearEq($year)
            ->active()
            ->get()
            ->map(fn ($allocation) => [
                'ref_cod_servidor' => $allocation->ref_cod_servidor,
                'ref_cod_servidor_funcao' => $allocation->ref_cod_servidor_funcao,
            ])
            ->toArray();
    }

    private function allocationExists(EmployeeAllocation $allocation, array $existingAllocations): bool
    {
        return collect($existingAllocations)->contains(function ($existing) use ($allocation) {
            return $existing['ref_cod_servidor'] === $allocation->ref_cod_servidor
                && $existing['ref_cod_servidor_funcao'] === $allocation->ref_cod_servidor_funcao;
        });
    }

    private function replicateAllocation(EmployeeAllocation $allocation, int $destinationYear, string $type = 'alocacoes_servidores'): void
    {
        $newAllocation = $allocation->replicate();
        $newAllocation->ano = $destinationYear;
        $newAllocation->data_admissao = null;
        $newAllocation->data_saida = null;
        $newAllocation->save();

        $this->incrementCopyCounter($allocation->ref_cod_escola, $type);
    }

    private function createMultiGradeSchoolClass(array $originSchoolClass, int $destinationSchoolClassId): void
    {
        $schoolClassGrades = LegacySchoolClassGrade::query()
            ->where(['escola_id' => $originSchoolClass['ref_ref_cod_escola'], 'turma_id' => $originSchoolClass['cod_turma']])
            ->get();

        foreach ($schoolClassGrades as $schoolClassGrade) {
            LegacySchoolClassGrade::create([
                'escola_id' => $originSchoolClass['ref_ref_cod_escola'],
                'serie_id' => $schoolClassGrade->serie_id,
                'turma_id' => $destinationSchoolClassId,
                'boletim_id' => $schoolClassGrade->boletim_id,
                'boletim_diferenciado_id' => $schoolClassGrade->boletim_diferenciado_id,
            ]);
        }
    }

    private function bulkCreateDisciplines(Collection $disciplines, int $destinationSchoolClassId): void
    {
        $disciplinesData = $disciplines->map(fn ($discipline) => [
            'componente_curricular_id' => $discipline->componente_curricular_id,
            'escola_id' => $discipline->escola_id,
            'carga_horaria' => $discipline->carga_horaria,
            'turma_id' => $destinationSchoolClassId,
            'ano_escolar_id' => $discipline->ano_escolar_id,
        ])->toArray();

        if (!empty($disciplinesData)) {
            LegacyDisciplineSchoolClass::insert($disciplinesData);
        }
    }

    private function bulkCreateSchoolClassModules(Collection $modules, int $destinationSchoolClassId, int $originYear, int $destinationYear): void
    {
        $modulesData = $modules->map(function ($module) use ($originYear, $destinationYear, $destinationSchoolClassId) {
            $dataInicio = $this->adjustDateForYear($module->data_inicio, $originYear, $destinationYear);
            $dataFim = $this->adjustDateForYear($module->data_fim, $originYear, $destinationYear);

            return [
                'ref_cod_modulo' => $module->ref_cod_modulo,
                'sequencial' => $module->sequencial,
                'ref_cod_turma' => $destinationSchoolClassId,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'dias_letivos' => $module->dias_letivos,
            ];
        })->toArray();

        if (!empty($modulesData)) {
            LegacySchoolClassStage::insert($modulesData);
        }
    }

    private function adjustDateForYear(string $date, int $originYear, int $destinationYear): string
    {
        $adjustedDate = str_replace($originYear, $destinationYear, $date);

        try {
            if ($this->isLeapYear($adjustedDate)) {
                $adjustedDate = str_replace('-02-29', '-02-28', $adjustedDate);
            }
        } catch (\Exception $e) {
        }

        return $adjustedDate;
    }

    private function isLeapYear(string $date): bool
    {
        try {
            $year = Carbon::createFromFormat('Y-m-d', $date)->year;

            return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function prepareDateRanges(array $startDates, array $endDates): array
    {
        return collect($startDates)->map(fn ($startDate, $key) => [
            'start' => Carbon::createFromFormat('d/m/Y', $startDate),
            'end' => Carbon::createFromFormat('d/m/Y', $endDates[$key]),
        ])->toArray();
    }

    private function hasDateConflict(Carbon $startDate, Carbon $endDate, int $year, int $schoolId): bool
    {
        $existingStage = LegacyAcademicYearStage::query()
            ->where('ref_ano', '!=', $year)
            ->where('ref_ref_cod_escola', $schoolId)
            ->whereRaw('(?::date BETWEEN data_inicio AND data_fim::date OR ?::date BETWEEN data_inicio AND data_fim OR (?::date <= data_inicio AND ?::date >= data_fim))', [
                $startDate, $endDate, $startDate, $endDate,
            ])
            ->limit(1)
            ->exists();

        return $existingStage;
    }

    private function getExistingStagesCount(int $year, int $schoolId): int
    {
        return LegacyAcademicYearStage::query()
            ->where('ref_ano', $year)
            ->where('ref_ref_cod_escola', $schoolId)
            ->count();
    }

    private function hasDataInStages(array $stagesToRemove, int $year, int $schoolId): bool
    {
        $count1 = LegacyDisciplineAbsence::query()
            ->join('modules.falta_aluno as fa', 'fa.id', 'modules.falta_componente_curricular.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', 'fa.matricula_id')
            ->whereIn('modules.falta_componente_curricular.etapa', $stagesToRemove)
            ->where(['m.ref_ref_cod_escola' => $schoolId, 'm.ano' => $year, 'm.ativo' => 1])
            ->count();

        if ($count1 > 0) {
            return true;
        }

        $count2 = LegacyGeneralAbsence::query()
            ->join('modules.falta_aluno as fa', 'fa.id', 'modules.falta_geral.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', 'fa.matricula_id')
            ->whereIn('modules.falta_geral.etapa', $stagesToRemove)
            ->where(['m.ref_ref_cod_escola' => $schoolId, 'm.ano' => $year, 'm.ativo' => 1])
            ->count();

        if ($count2 > 0) {
            return true;
        }

        $count3 = LegacyDisciplineScore::query()
            ->join('modules.nota_aluno as na', 'na.id', 'modules.nota_componente_curricular.nota_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', 'na.matricula_id')
            ->whereIn('modules.nota_componente_curricular.etapa', $stagesToRemove)
            ->where(['m.ref_ref_cod_escola' => $schoolId, 'm.ano' => $year, 'm.ativo' => 1])
            ->count();

        return $count3 > 0;
    }

    private function hasOnlineDiaryData(array $stagesToRemove, int $schoolId, int $year): bool
    {
        if (!config('legacy.config.url_novo_educacao') || !config('legacy.config.token_novo_educacao')) {
            return false;
        }

        $iDiarioService = app(iDiarioService::class);

        foreach ($stagesToRemove as $stage) {
            if ($iDiarioService->getStepActivityByUnit($schoolId, $year, $stage)) {
                return true;
            }
        }

        return false;
    }

    private function copyEvaluationRulesForNewYear(int $schoolId, int $previousYear, int $year): void
    {
        $gradesToProcess = LegacySchoolGrade::query()
            ->where('ref_cod_escola', $schoolId)
            ->whereRaw('? = ANY(anos_letivos)', [$previousYear])
            ->distinct()
            ->pluck('ref_cod_serie');

        $allExistingRules = LegacyEvaluationRuleGradeYear::query()
            ->whereIn('serie_id', $gradesToProcess)
            ->where('ano_letivo', $previousYear)
            ->get()
            ->groupBy('serie_id');

        $existingRulesForNewYear = LegacyEvaluationRuleGradeYear::query()
            ->whereIn('serie_id', $gradesToProcess)
            ->where('ano_letivo', $year)
            ->pluck('serie_id')
            ->toArray();

        foreach ($gradesToProcess as $gradeId) {
            if (in_array($gradeId, $existingRulesForNewYear)) {
                continue;
            }

            $rulesForGrade = $allExistingRules->get($gradeId, []);

            foreach ($rulesForGrade as $rule) {
                LegacyEvaluationRuleGradeYear::create([
                    'serie_id' => $gradeId,
                    'regra_avaliacao_id' => $rule->regra_avaliacao_id,
                    'regra_avaliacao_diferenciada_id' => $rule->regra_avaliacao_diferenciada_id,
                    'ano_letivo' => $year,
                ]);
            }
        }
    }

    private function performDataCopy(int $schoolId, int $year, bool $copySchoolClasses, bool $copyTeacherData, bool $copyEmployeeData, ?int $userId): void
    {
        if ($copySchoolClasses) {
            $this->copySchoolClassesFromPreviousYear($schoolId, $year, $copyTeacherData, $userId);
        }

        if ($copyEmployeeData) {
            $this->copyEmployeeAllocations($schoolId, $year, false);
        }

        $this->executeCopyAcademicYears($year, $schoolId);
    }

    private function createSchoolAcademicYear(int $schoolId, int $year, bool $copyTeacherData, bool $copyEmployeeData, ?int $userId): LegacySchoolAcademicYear
    {
        $existingAcademicYear = LegacySchoolAcademicYear::query()
            ->whereSchool($schoolId)
            ->whereYearEq($year)
            ->first();

        if ($existingAcademicYear) {
            $existingAcademicYear->update([
                'ref_usuario_cad' => $userId,
                'andamento' => 0,
                'ativo' => 1,
                'turmas_por_ano' => 1,
                'copia_dados_professor' => $copyTeacherData,
                'copia_dados_demais_servidores' => $copyEmployeeData,
                'copia_turmas' => true,
            ]);

            return $existingAcademicYear->fresh();
        }

        return LegacySchoolAcademicYear::create([
            'ref_cod_escola' => $schoolId,
            'ano' => $year,
            'ref_usuario_cad' => $userId,
            'andamento' => 0,
            'ativo' => 1,
            'turmas_por_ano' => 1,
            'copia_dados_professor' => $copyTeacherData,
            'copia_dados_demais_servidores' => $copyEmployeeData,
            'copia_turmas' => true,
        ]);
    }

    private function createAcademicYearStages(
        LegacySchoolAcademicYear $academicYear,
        array $startDates,
        array $endDates,
        array $schoolDays,
        int $moduleId,
        int $schoolId,
        int $year
    ): void {
        $stagesData = [];

        foreach ($startDates as $key => $startDate) {
            $diasLetivos = $schoolDays[$key] ?? null;
            if ($diasLetivos === '') {
                $diasLetivos = null;
            }

            $data = [
                'ref_ano' => $year,
                'ref_ref_cod_escola' => $schoolId,
                'sequencial' => $key + 1,
                'ref_cod_modulo' => $moduleId,
                'data_inicio' => dataToBanco($startDate),
                'data_fim' => dataToBanco($endDates[$key]),
                'dias_letivos' => $diasLetivos,
            ];

            if (!$this->validateAcademicYearModuleData($data)) {
                throw new AcademicYearServiceException('Dados do módulo inválidos para a escola ' . $schoolId . ' na etapa ' . ($key + 1) . '.');
            }

            $stagesData[] = array_merge($data, [
                'escola_ano_letivo_id' => $academicYear->getKey(),
            ]);
        }

        LegacyAcademicYearStage::insert($stagesData);

        LegacySchoolAcademicYear::query()
            ->where('ref_cod_escola', $schoolId)
            ->where('ano', $year)
            ->where('ativo', 0)
            ->update(['ativo' => 1]);
    }

    private function getSchoolAcademicYear(int $schoolId, int $year): LegacySchoolAcademicYear
    {
        $schoolAcademicYear = LegacySchoolAcademicYear::query()
            ->whereSchool($schoolId)
            ->whereYearEq($year)
            ->first();

        if (!$schoolAcademicYear) {
            throw new AcademicYearServiceException('Ano letivo não encontrado para a escola ' . $schoolId . ' no ano ' . $year);
        }

        return $schoolAcademicYear;
    }

    private function activateAcademicYear(int $schoolId, int $year): void
    {
        LegacySchoolAcademicYear::query()
            ->whereSchool($schoolId)
            ->whereYearEq($year)
            ->where('ativo', 0)
            ->update(['ativo' => 1]);
    }

    private function validateBatchParams(array $params): array
    {
        if (empty($params['schools']) || empty($params['periodos']) || empty($params['year'])) {
            return $this->createBatchValidationResult(false, 'Parâmetros de escola, períodos e ano letivo são obrigatórios.');
        }

        if (!is_array($params['schools']) || !is_array($params['periodos'])) {
            return $this->createBatchValidationResult(false, 'Parâmetros de escola e períodos devem ser arrays.');
        }

        if (!is_numeric($params['year']) || (int) $params['year'] != $params['year']) {
            return $this->createBatchValidationResult(false, 'Ano letivo deve ser um número inteiro.');
        }

        return $this->createBatchValidationResult(true, 'Parâmetros válidos.');
    }

    private function createBatchValidationResult(bool $valid, string $message): array
    {
        return [
            'valid' => $valid,
            'message' => $message,
        ];
    }

    private function createBatchFailureResult(int $total, int $processed, array $errors, string $message, $year): array
    {
        return [
            'status' => 'failed',
            'total' => $total,
            'processed' => $processed,
            'year' => (int) $year,
            'errors' => $errors,
            'details' => [],
            'message' => $message,
        ];
    }

    private function calculateBatchTotal(array $params): int
    {
        return count($params['schools']);
    }

    private Collection $batchSchools;

    private array $copyCounters = [];

    private function initializeCopyCounters(int $schoolId): void
    {
        $this->copyCounters[$schoolId] = [
            'turmas' => 0,
            'alocacoes_professores' => 0,
            'alocacoes_servidores' => 0,
            'vinculos_professores' => 0,
        ];
    }

    private function resetCopyCounters(): void
    {
        $this->copyCounters = [];
    }

    private function incrementCopyCounter(int $schoolId, string $type): void
    {
        if (!isset($this->copyCounters[$schoolId])) {
            $this->initializeCopyCounters($schoolId);
        }

        $this->copyCounters[$schoolId][$type]++;
    }

    private function loadBatchCollections(array $params): void
    {
        $schoolIds = array_map('intval', $params['schools']);

        $schoolsWithNames = LegacySchool::query()
            ->selectRaw('cod_escola, relatorio.get_nome_escola(cod_escola) as nome')
            ->whereIn('cod_escola', $schoolIds)
            ->where('ativo', 1)
            ->get();

        $this->batchSchools = $schoolsWithNames->keyBy('cod_escola');
    }

    private function validateAllSchools(array $params): array
    {
        $validatedData = [];
        $errors = [];
        $skippedSchools = [];

        $existingSchools = $this->getExistingAcademicYearSchools($params['schools'], $params['year']);
        $schoolsToProcess = array_diff($params['schools'], $existingSchools);

        foreach ($existingSchools as $schoolId) {
            $school = $this->batchSchools->get($schoolId);
            $schoolName = $school ? $school->nome : "Escola ID {$schoolId}";

            $skippedSchools[] = [
                'school_id' => $schoolId,
                'school_name' => $schoolName,
                'type' => 'skipped',
                'message' => "Escola '{$schoolName}': Ano letivo {$params['year']} já está aberto",
            ];
        }

        foreach ($schoolsToProcess as $schoolId) {
            $validationResult = $this->validateSchoolForBatch($schoolId, $params);

            if ($validationResult['success']) {
                $validatedData[] = $validationResult['data'];
            } else {
                $errors[] = $validationResult['error'];
            }
        }

        return [
            'validatedData' => $validatedData,
            'errors' => $errors,
            'skippedSchools' => $skippedSchools,
        ];
    }

    private function validateSchoolForBatch(int $schoolId, array $params): array
    {
        try {
            $school = $this->batchSchools->get($schoolId);

            if (!$school) {
                return [
                    'success' => false,
                    'error' => [
                        'school_id' => $schoolId,
                        'school_name' => "Escola ID {$schoolId}",
                        'type' => 'error',
                        'error' => "Escola ID {$schoolId} não encontrada ou inativa.",
                    ],
                ];
            }

            $validPeriodos = collect($params['periodos'])->filter(function ($periodo) {
                return !empty($periodo['data_inicio']) && !empty($periodo['data_fim']);
            });

            $startDates = $validPeriodos->pluck('data_inicio')->toArray();
            $endDates = $validPeriodos->pluck('data_fim')->toArray();

            $this->validateAcademicYearDates($startDates, $endDates, $params['year'], $schoolId);

            $this->validateAcademicYearModules($params['year'], $schoolId, $validPeriodos->count());

            return [
                'success' => true,
                'data' => [
                    'school' => $school,
                    'school_id' => $schoolId,
                    'params' => $params,
                ],
            ];

        } catch (\Exception $e) {
            $schoolName = $school ? $school->nome : "Escola ID {$schoolId}";

            return [
                'success' => false,
                'error' => [
                    'school_id' => $schoolId,
                    'school_name' => $schoolName,
                    'type' => 'error',
                    'error' => "Escola '{$schoolName}': {$e->getMessage()}",
                ],
            ];
        }
    }

    private function processBatchValidData(array $validatedData, array $skippedSchools, array $params, int $total): array
    {
        $processed = 0;
        $errors = [];
        $details = [];

        $this->resetCopyCounters();

        foreach ($skippedSchools as $skipped) {
            $details[] = [
                'type' => 'skipped',
                'message' => $skipped['message'],
                'school_id' => $skipped['school_id'],
                'school_name' => $skipped['school_name'],
            ];
        }
        DB::beginTransaction();
        foreach ($validatedData as $data) {
            $result = $this->processSchoolAcademicYear($data['school'], $data['params']);
            $processed++;
            $copyInfo = $this->buildCopyInfoMessage($result['copyResults'], $data['params']);
            $message = "Escola '{$data['school']->nome}': Ano letivo {$params['year']} criado com sucesso";
            if (!empty($copyInfo)) {
                $message .= '. ' . $copyInfo;
            }
            $details[] = [
                'type' => 'success',
                'message' => $message,
                'school_id' => $data['school']->cod_escola,
                'school_name' => $data['school']->nome,
            ];
        }
        DB::commit();
        $skippedCount = count($skippedSchools);
        $message = 'Processamento concluído. ';
        if ($processed > 0) {
            $message .= "{$processed} escola(s) processada(s). ";
        }
        if ($skippedCount > 0) {
            $message .= "{$skippedCount} escola(s) ignorada(s).";
        }

        return [
            'status' => 'completed',
            'total' => $total,
            'processed' => $processed,
            'skipped' => $skippedCount,
            'year' => $params['year'],
            'errors' => $errors,
            'details' => $details,
            'message' => $message,
        ];
    }

    private function processSchoolAcademicYear($school, array $params): array
    {
        $validPeriodos = collect($params['periodos'])->filter(function ($periodo) {
            return !empty($periodo['data_inicio']) && !empty($periodo['data_fim']);
        });

        $startDates = $validPeriodos->pluck('data_inicio')->toArray();
        $endDates = $validPeriodos->pluck('data_fim')->toArray();
        $schoolDays = $validPeriodos->pluck('dias_letivos')->toArray();

        $this->createAcademicYearForSchool(
            schoolId: $school->cod_escola,
            year: $params['year'],
            startDates: $startDates,
            endDates: $endDates,
            schoolDays: $schoolDays,
            moduleId: $params['moduleId'],
            copySchoolClasses: $params['copySchoolClasses'],
            copyTeacherData: $params['copyTeacherData'],
            copyEmployeeData: $params['copyEmployeeData'],
            userId: $params['user']->id
        );

        $copyResults = $this->getCopyResults($school->cod_escola, $params['year'], $params);

        return [
            'action' => 'criado',
            'copyResults' => $copyResults,
        ];
    }

    private function getCopyResults(int $schoolId, int $year, array $params): array
    {
        $copyResults = [];

        if (!isset($this->copyCounters[$schoolId])) {
            return $copyResults;
        }

        $counters = $this->copyCounters[$schoolId];

        if ($params['copySchoolClasses']) {
            $copyResults['turmas'] = $counters['turmas'];
            $copyResults['vinculos_professores'] = $counters['vinculos_professores'];
        }

        if ($params['copyTeacherData']) {
            $copyResults['alocacoes_professores'] = $counters['alocacoes_professores'];
        }

        if ($params['copyEmployeeData']) {
            $copyResults['alocacoes_servidores'] = $counters['alocacoes_servidores'];
        }

        return $copyResults;
    }

    private function buildCopyInfoMessage(array $copyResults, array $params): string
    {
        $messages = [
            'turmas' => [
                'positive' => 'copiadas {count} turma(s)',
                'negative' => 'nenhuma turma copiada',
            ],
            'alocacoes_professores' => [
                'positive' => 'copiadas {count} alocação(ões) de professor(es)',
                'negative' => 'nenhuma alocação de professor copiada',
            ],
            'vinculos_professores' => [
                'positive' => 'copiados {count} vínculo(s) de professor(es) com turmas',
                'negative' => 'nenhum vínculo de professor copiado',
            ],
            'alocacoes_servidores' => [
                'positive' => 'copiadas {count} alocação(ões) dos demais servidor(es)',
                'negative' => 'nenhuma alocação de servidor copiada',
            ],
        ];

        $copyInfo = array_map(function ($key, $templates) use ($copyResults) {
            if (!isset($copyResults[$key])) {
                return null;
            }

            $count = $copyResults[$key];

            return $count > 0
                ? str_replace('{count}', $count, $templates['positive'])
                : $templates['negative'];
        }, array_keys($messages), $messages);

        return implode(', ', array_filter($copyInfo));
    }
}
