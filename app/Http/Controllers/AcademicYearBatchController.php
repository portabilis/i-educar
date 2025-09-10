<?php

namespace App\Http\Controllers;

use App\Exceptions\AcademicYearServiceException;
use App\Process;
use App\Services\AcademicYearService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AcademicYearBatchController extends Controller
{
    public function __construct(
        private readonly AcademicYearService $academicYearService
    ) {}

    /**
     * @throws \Exception
     */
    public function process(Request $request): JsonResponse
    {
        try {
            $this->cleanRequestData($request);
            $this->validateRequest($request);

            $processedData = $this->processAcademicYearData($request);

            $params = [
                'year' => $request->get('ano'),
                'schools' => $processedData['allSchools']->toArray(),
                'periodos' => $processedData['periodos'],
                'moduleId' => $request->get('ref_cod_modulo'),
                'user' => $request->user(),
                'copySchoolClasses' => $processedData['copySchoolClasses'],
                'copyTeacherData' => $processedData['copyTeacherData'],
                'copyEmployeeData' => $processedData['copyEmployeeData'],
            ];

            $result = $this->academicYearService->processAcademicYearBatch($params);

            return $this->handleServiceResult($result);

        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $messages) {
                foreach ($messages as $message) {
                    $errors[] = ['error' => $message];
                }
            }

            return $this->errorResponse('Erro de validação dos campos', $errors);
        } catch (AcademicYearServiceException $e) {
            return $this->errorResponse('Erro de validação', [['error' => $e->getMessage()]]);
        }
    }

    private function cleanRequestData(Request $request): void
    {
        $schools = $request->get('escola', []);
        $periodos = $request->get('periodos', []);

        $cleanedSchools = collect($schools)->flatten()->filter()->values()->toArray();
        $cleanedPeriodos = $this->filterValidPeriodos($periodos);

        $request->merge([
            'escola' => $cleanedSchools,
            'periodos' => $cleanedPeriodos,
        ]);
    }

    private function filterValidPeriodos(array $periodos): array
    {
        return collect($periodos)->filter(function ($periodo) {
            return !empty($periodo['data_inicio']) || !empty($periodo['data_fim']) || !empty($periodo['dias_letivos']);
        })->values()->toArray();
    }

    private function validateRequest(Request $request): void
    {
        $validator = Validator::make(
            data: $request->all(),
            rules: [
                'ano' => ['required', 'integer', 'digits:4', 'min:1900'],
                'ref_cod_instituicao' => ['required', 'integer'],
                'escola' => ['required', 'array', 'min:1'],
                'escola.*' => ['integer', 'exists:escola,cod_escola'],
                'ref_cod_modulo' => ['required', 'integer', 'exists:modulo,cod_modulo'],
                'periodos' => ['required', 'array', 'min:1'],
                'periodos.*.data_inicio' => ['nullable', 'date_format:d/m/Y'],
                'periodos.*.data_fim' => ['nullable', 'date_format:d/m/Y'],
                'periodos.*.dias_letivos' => ['nullable', 'integer', 'min:1', 'max:366'],
            ],
            attributes: [
                'ano' => 'ano',
                'ref_cod_instituicao' => 'instituição',
                'escola' => 'escola',
                'escola.*' => 'escola',
                'ref_cod_modulo' => 'módulo',
                'periodos' => 'períodos',
                'periodos.*.data_inicio' => 'data de início',
                'periodos.*.data_fim' => 'data de fim',
                'periodos.*.dias_letivos' => 'dias letivos',
            ]
        );

        $validator->validate();

        $this->validatePeriodosConsistency($request->get('periodos', []));
    }

    private function validatePeriodosConsistency(array $periodos): void
    {
        $errors = [];

        foreach ($periodos as $index => $periodo) {
            $hasDataInicio = !empty($periodo['data_inicio']);
            $hasDataFim = !empty($periodo['data_fim']);

            if ($hasDataInicio && !$hasDataFim) {
                $errors[] = 'Período ' . ($index + 1) . ': Data final é obrigatória quando data inicial está preenchida';
            }

            if (!$hasDataInicio && $hasDataFim) {
                $errors[] = 'Período ' . ($index + 1) . ': Data inicial é obrigatória quando data final está preenchida';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException(
                validator([], []),
                response()->json(['errors' => $errors], 422)
            );
        }
    }

    private function processAcademicYearData(Request $request): array
    {
        $schools = collect($request->get('escola', []))->filter()->values();
        $periodos = collect($this->filterValidPeriodos($request->get('periodos', [])));

        $isAdmin = Auth::check() ? Auth::user()->isAdmin() : false;

        // Processar checkboxes de forma consistente
        $copySchoolClasses = !$isAdmin || $request->boolean('copiar_turmas');
        $copyTeacherData = $request->boolean('copiar_alocacoes_e_vinculos_professores');
        $copyEmployeeData = $request->boolean('copiar_alocacoes_demais_servidores');

        return [
            'allSchools' => $schools,
            'periodos' => $periodos->toArray(),
            'copySchoolClasses' => $copySchoolClasses,
            'copyTeacherData' => $copyTeacherData,
            'copyEmployeeData' => $copyEmployeeData,
        ];
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
            session()->flash('academic_year_batch_result', $result);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'redirect' => route('academic-year.status'),
            ]);
        }
    }

    private function errorResponse(string $message, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ]);
    }

    public function status()
    {
        $result = session('academic_year_batch_result', []);

        session()->forget('academic_year_batch_result');

        $this->breadcrumb('Resultado do Ano Letivo em Lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
            route('academic-year.edit') => 'Ano Letivo em Lote',
        ]);

        $this->menu(Process::ACADEMIC_YEAR_IMPORT);

        return view('academic-year.status', ['result' => $result]);
    }

    public function edit()
    {
        $this->menu(Process::ACADEMIC_YEAR_IMPORT);
        $this->breadcrumb('Ano Letivo em Lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
            null => 'On-Boarding',
        ]);

        return view('academic-year.edit', ['user' => request()->user()]);
    }
}
