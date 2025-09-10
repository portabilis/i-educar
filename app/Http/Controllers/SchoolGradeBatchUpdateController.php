<?php

namespace App\Http\Controllers;

use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Process;
use App\Services\SchoolGradeImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SchoolGradeBatchUpdateController extends Controller
{
    protected SchoolGradeImportService $service;

    public function __construct(SchoolGradeImportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $this->breadcrumb('Atualização de séries da escola em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::SCHOOL_GRADE);

        return view('school-grade.batch-update.index', [
            'user' => $request->user(),
        ]);
    }

    public function preview(Request $request)
    {
        try {
            $year = $request->query('ano');
            $schoolGradeData = $request->query('escola_serie', []);
            $blockEnrollment = $request->query('bloquear_enturmacao_sem_vagas', 0);
            $blockRegistration = $request->query('bloquear_cadastro_turma_para_serie_com_vagas', 0);

            if (empty($year) || empty($schoolGradeData)) {
                return $this->errorResponse('Ano e dados de escola/série são obrigatórios.');
            }

            $processedData = $this->processSchoolGradeData($schoolGradeData);
            $previewData = $this->buildPreviewData($year, $processedData, $blockEnrollment, $blockRegistration);

            return response()->json([
                'status' => 'success',
                'preview' => $previewData,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao processar preview.');
        }
    }

    public function process(Request $request)
    {
        try {
            $this->cleanRequestData($request);
            $this->validateRequest($request);

            $schoolGradeData = $request->get('escola_serie', []);
            $processedData = $this->processSchoolGradeData($schoolGradeData);

            $params = [
                'year' => $request->get('ano'),
                'schools' => $processedData['allSchools']->toArray(),
                'grades' => $processedData['allGrades']->toArray(),
                'escola_serie_data' => $schoolGradeData,
                'user' => $request->user(),
                'bloquear_enturmacao_sem_vagas' => $request->get('bloquear_enturmacao_sem_vagas', 0),
                'bloquear_cadastro_turma_para_serie_com_vagas' => $request->get('bloquear_cadastro_turma_para_serie_com_vagas', 0),
            ];

            $result = $this->service->processSchoolGradeUpdate($params);

            return $this->handleServiceResult($result);
        } catch (\Exception $e) {
            error_log('Erro ao processar atualização em lote de séries: ' . $e->getMessage());

            return $this->errorResponse('Erro ao processar atualização. Por favor, tente novamente.');
        }
    }

    public function status()
    {
        $result = session('batch_update_result', []);

        session()->forget('batch_update_result');

        $this->breadcrumb('Resultado da Atualização em Lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
            route('school-grade.batch-update.index') => 'Atualização de séries da escola em lote',
        ]);

        $this->menu(Process::SCHOOL_GRADE);

        return view('school-grade.batch-update.status', ['result' => $result]);
    }

    private function cleanRequestData(Request $request): void
    {
        $schoolGradeData = $request->get('escola_serie', []);

        $cleanedSchoolGradeData = collect($schoolGradeData)
            ->filter(fn ($line) => !empty($line['escolas'] ?? []) && !empty($line['series'] ?? []))
            ->map(fn ($line) => [
                'escolas' => $line['escolas'],
                'series' => $line['series'],
            ])
            ->toArray();

        $allSchools = collect($cleanedSchoolGradeData)->pluck('escolas')->flatten()->unique()->values();
        $allGrades = collect($cleanedSchoolGradeData)->pluck('series')->flatten()->unique()->values();

        $request->merge([
            'escola_serie' => $cleanedSchoolGradeData,
            'escola' => $allSchools->toArray(),
            'series' => $allGrades->toArray(),
        ]);
    }

    private function validateRequest(Request $request): void
    {
        $previousYear = now()->year - 1;
        $nextYear = now()->year + 1;

        $request->validate([
            'ano' => "required|integer|min:{$previousYear}|max:{$nextYear}",
            'escola' => 'required|array|min:1',
            'escola.*' => 'integer|exists:escola,cod_escola',
            'series' => 'required|array|min:1',
            'series.*' => 'integer|exists:serie,cod_serie',
            'bloquear_enturmacao_sem_vagas' => 'nullable|boolean',
            'bloquear_cadastro_turma_para_serie_com_vagas' => 'nullable|boolean',
        ]);
    }

    private function processSchoolGradeData(array $schoolGradeData): array
    {
        $validLines = collect($schoolGradeData)
            ->filter(fn ($line) => !empty($line['escolas'] ?? []) && !empty($line['series'] ?? []));

        $lineData = $validLines->map(fn ($line, $lineNumber) => [
            'line' => $lineNumber,
            'schools' => $line['escolas'],
            'grades' => $line['series'],
        ]);

        $allSchools = $validLines->pluck('escolas')->flatten()->unique()->values();
        $allGrades = $validLines->pluck('series')->flatten()->unique()->values();

        $totalCombinations = $validLines->sum(fn ($line) => count($line['escolas']) * count($line['series']));

        return [
            'allSchools' => $allSchools,
            'allGrades' => $allGrades,
            'lineData' => $lineData,
            'totalCombinations' => $totalCombinations,
        ];
    }

    private function buildPreviewData(string $year, array $processedData, int $blockEnrollment, int $blockRegistration): array
    {
        $allSchools = $processedData['allSchools'];
        $allGrades = $processedData['allGrades'];
        $lineData = $processedData['lineData'];
        $totalCombinations = $processedData['totalCombinations'];

        $schoolsData = LegacySchool::whereIn('cod_escola', $allSchools)
            ->where('ativo', 1)
            ->with(['person', 'organization'])
            ->orderBy('cod_escola')
            ->get()
            ->sortBy('name');

        $gradesData = LegacyGrade::whereIn('cod_serie', $allGrades)
            ->where('ativo', 1)
            ->with(['course' => function ($query) {
                $query->orderBy('nm_curso');
            }])
            ->orderBy('nm_serie')
            ->orderBy('ref_cod_curso')
            ->get(['cod_serie', 'nm_serie', 'ref_cod_curso']);

        $tableData = $this->buildTableData($lineData->toArray(), $schoolsData, $gradesData);

        return [
            'year' => $year,
            'total_combinations' => $totalCombinations,
            'total_lines' => $lineData->count(),
            'table_data' => $tableData->toArray(),
            'line_data' => $lineData->toArray(),
            'blocking_params' => [
                'bloquear_enturmacao_sem_vagas' => $blockEnrollment,
                'bloquear_cadastro_turma_para_serie_com_vagas' => $blockRegistration,
            ],
        ];
    }

    private function errorResponse(string $message): JsonResponse
    {
        session()->flash('error', $message);

        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => [['error' => $message]],
        ]);
    }

    private function handleServiceResult(array $result): JsonResponse
    {
        $errors = collect($result['errors'] ?? [])->unique('error')->values()->all();
        $details = $result['details'] ?? [];

        if ($result['status'] === 'failed') {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
                'errors' => $errors,
                'details' => $details,
            ]);
        } else {
            session()->flash('batch_update_result', $result);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'redirect' => route('school-grade.batch-update.status'),
            ]);
        }
    }

    private function buildTableData(array $lineData, $schoolsData, $gradesData): Collection
    {
        $tableData = collect();
        foreach ($lineData as $line) {
            $lineSchools = $schoolsData->whereIn('cod_escola', $line['schools']);
            $lineGrades = $gradesData->whereIn('cod_serie', $line['grades']);

            foreach ($lineSchools as $school) {
                $schoolRow = [
                    'school' => [
                        'id' => $school->cod_escola,
                        'name' => strtoupper($school->name ?? 'Escola não encontrada'),
                    ],
                    'courses' => [],
                ];

                $lineGradesByCourse = $lineGrades->groupBy('ref_cod_curso')->map(function ($grades) {
                    return $grades->sortBy('nm_serie');
                })->sortKeys();

                foreach ($lineGradesByCourse as $courseId => $grades) {
                    $course = $grades->first()->course;
                    $seriesNames = $grades->pluck('nm_serie')->implode('<br>');

                    $schoolRow['courses'][] = [
                        'course_name' => $course->nm_curso ?? 'Curso não encontrado',
                        'series' => $seriesNames,
                    ];
                }

                $tableData->push($schoolRow);
            }
        }

        return $tableData;
    }
}
