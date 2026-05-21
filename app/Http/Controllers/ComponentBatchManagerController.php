<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComponentBatchOperationRequest;
use App\Jobs\ComponentBatchOperationJob;
use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\NotificationType;
use App\Process;
use App\Services\ComponentBatchManagerService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ComponentBatchManagerController extends Controller
{
    public function index(Request $request)
    {
        $this->breadcrumb('Gerenciamento em Lote de Componentes', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
        $this->menu(Process::COMPONENT_BATCH_MANAGER);

        $operations = ComponentBatchOperation::query()
            ->orderByDesc('created_at')
            ->paginate(20);

        $operations->getCollection()->transform(function ($operation) {
            $status = $operation->status();
            $isStale = in_array($status, [ComponentBatchStatus::WAITING, ComponentBatchStatus::RUNNING])
                && $operation->created_at < now()->subMinutes(20);

            $operation->view_status = $status;
            $operation->view_is_stale = $isStale;

            return $operation;
        });

        return view('component-batch-manager.index', [
            'operations' => $operations,
        ]);
    }

    public function create(Request $request)
    {
        $this->breadcrumb('Gerenciamento em Lote de Componentes', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
        $this->menu(Process::COMPONENT_BATCH_MANAGER);

        $preview = $request->has('restore') ? session('batch_operation_preview_data') : null;

        if ($preview) {
            session()->flashInput([
                'ano' => $preview['year'],
                'ref_cod_instituicao' => $preview['institution_id'],
                'escola' => $preview['school_ids'],
                'curso' => $preview['course_ids'],
                'ref_cod_serie' => $preview['grade_ids'],
                'discipline_ids' => $preview['discipline_ids'],
                'remove_records' => $preview['remove_records'] ?? false,
                'remove_exemptions' => $preview['remove_exemptions'] ?? false,
                'unlink_class_components' => $preview['unlink_class_components'] ?? false,
                'unlink_teacher_disciplines' => $preview['unlink_teacher_disciplines'] ?? false,
                'unlink_school_grade_disciplines' => $preview['unlink_school_grade_disciplines'] ?? false,
                'unlink_grade_components' => $preview['unlink_grade_components'] ?? false,
            ]);
        }

        return view('component-batch-manager.create');
    }

    public function apiCourses(Request $request)
    {
        $schoolIds = array_filter((array) $request->input('school_ids', []));
        $year = $request->input('year');

        if (empty($schoolIds)) {
            return response()->json([]);
        }

        $courses = LegacyCourse::query()
            ->active()
            ->join('pmieducar.escola_curso as ec', 'ec.ref_cod_curso', 'curso.cod_curso')
            ->where('ec.ativo', 1)
            ->whereIn('ec.ref_cod_escola', $schoolIds)
            ->when($year, fn ($q) => $q->whereRaw('ARRAY[?::smallint] <@ ec.anos_letivos', [$year]))
            ->select('curso.cod_curso', 'curso.nm_curso')
            ->distinct()
            ->orderBy('curso.nm_curso')
            ->pluck('nm_curso', 'cod_curso');

        return response()->json($courses);
    }

    public function apiGrades(Request $request)
    {
        $schoolIds = array_filter((array) $request->input('school_ids', []));
        $courseIds = array_filter((array) $request->input('course_ids', []));
        $year = $request->input('year');

        if (empty($courseIds)) {
            return response()->json([]);
        }

        $grades = LegacyGrade::query()
            ->active()
            ->when(!empty($schoolIds), fn ($q) => $q
                ->join('pmieducar.escola_serie as es', 'es.ref_cod_serie', 'serie.cod_serie')
                ->where('es.ativo', 1)
                ->whereIn('es.ref_cod_escola', $schoolIds)
                ->when($year, fn ($q) => $q->whereRaw('ARRAY[?::smallint] <@ es.anos_letivos', [$year]))
            )
            ->when(!empty($courseIds), fn ($q) => $q->whereIn('serie.ref_cod_curso', $courseIds))
            ->select('serie.cod_serie', 'serie.nm_serie')
            ->distinct()
            ->orderBy('serie.nm_serie')
            ->pluck('nm_serie', 'cod_serie');

        return response()->json($grades);
    }

    public function apiDisciplines(Request $request)
    {
        $schoolIds = array_filter((array) $request->input('school_ids', []));
        $gradeIds = array_filter((array) $request->input('grade_ids', []));
        $year = $request->input('year');

        if (empty($gradeIds)) {
            return response()->json([]);
        }

        $disciplines = LegacySchoolGradeDiscipline::query()
            ->join('modules.componente_curricular as cc', 'cc.id', 'escola_serie_disciplina.ref_cod_disciplina')
            ->where('escola_serie_disciplina.ativo', 1)
            ->when(!empty($schoolIds), fn ($q) => $q->whereIn('escola_serie_disciplina.ref_ref_cod_escola', $schoolIds))
            ->when(!empty($gradeIds), fn ($q) => $q->whereIn('escola_serie_disciplina.ref_ref_cod_serie', $gradeIds))
            ->when($year, fn ($q) => $q->whereRaw('ARRAY[?::smallint] <@ escola_serie_disciplina.anos_letivos', [$year]))
            ->select('cc.id', 'cc.nome')
            ->distinct()
            ->orderBy('cc.nome')
            ->pluck('nome', 'id');

        return response()->json($disciplines);
    }

    public function preview(ComponentBatchOperationRequest $request)
    {
        $this->breadcrumb('Preview da Operação', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
            route('component-batch-manager.create') => 'Gerenciamento de Componentes',
        ]);
        $this->menu(Process::COMPONENT_BATCH_MANAGER);

        $validated = $request->validated();

        $service = app(ComponentBatchManagerService::class);
        $preview = $service->calculatePreview($validated);

        session(['batch_operation_preview_data' => array_merge($validated, [
            'preview_counts' => $preview,
        ])]);

        $warnings = $service->buildWarnings($validated, $preview);

        ['totalIeducar' => $totalIeducar, 'totalIdiario' => $totalIdiario] = ComponentBatchManagerService::sumPreviewCounts($preview);

        $blockingError = null;
        $protectionDetails = [];

        if (($preview['escola_serie_disciplina_skipped'] ?? 0) > 0 || ($preview['componente_ano_escolar_skipped'] ?? 0) > 0) {
            $protectionDetails = $service->getProtectionDetails($validated, $preview);
        } elseif ($totalIeducar === 0 && $totalIdiario === 0) {
            $blockingError = 'Nenhum registro encontrado para os filtros selecionados.';
        }

        return view('component-batch-manager.preview', array_merge(
            $this->resolveNames($validated),
            [
                'params' => $validated,
                'preview' => $preview,
                'warnings' => $warnings,
                'blockingError' => $blockingError,
                'protectionDetails' => $protectionDetails,
                'totalIeducar' => $totalIeducar,
                'totalIdiario' => $totalIdiario,
            ]
        ));
    }

    public function execute(Request $request)
    {
        $data = session('batch_operation_preview_data');

        if (!$data) {
            return redirect()->route('component-batch-manager.create')
                ->withErrors(['Sessão expirada. Refaça o processo.']);
        }

        $operation = ComponentBatchOperation::create([
            'user_id' => $request->user()->getKey(),
            'data' => array_merge($data, ['user_id' => $request->user()->getKey()]),
        ]);

        $job = new ComponentBatchOperationJob(
            operation: $operation,
            databaseConnection: DB::getDefaultConnection(),
        );
        $userId = $request->user()->getKey();

        Bus::batch([$job])
            ->then(function () use ($userId, $operation) {
                $operation->refresh();

                // Se está aguardando callback do iDiário, não notifica ainda
                if ($operation->status_id === ComponentBatchStatus::RUNNING->value) {
                    return;
                }

                (new NotificationService)->createByUser(
                    userId: $userId,
                    text: 'Remoção de componentes concluída com sucesso.',
                    link: route('component-batch-manager.show', $operation),
                    type: NotificationType::OTHER
                );
            })
            ->catch(function () use ($userId, $operation) {
                (new NotificationService)->createByUser(
                    userId: $userId,
                    text: 'Erro na remoção de componentes. Clique para ver detalhes.',
                    link: route('component-batch-manager.show', $operation),
                    type: NotificationType::OTHER
                );
            })
            ->dispatch();

        session()->forget('batch_operation_preview_data');

        return redirect()->route('component-batch-manager.show', $operation);
    }

    public function show(ComponentBatchOperation $componentBatchOperation, ComponentBatchManagerService $service)
    {
        $this->breadcrumb('Resultado da Operação', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
            route('component-batch-manager.create') => 'Gerenciamento de Componentes',
        ]);
        $this->menu(Process::COMPONENT_BATCH_MANAGER);

        $data = $componentBatchOperation->data;
        $status = $componentBatchOperation->status();
        $previewCounts = $data['preview_counts'] ?? [];
        $postCounts = $data['post_counts'] ?? null;
        $postIdiario = $data['post_idiario'] ?? null;
        $verificationWarnings = $data['verification_warnings'] ?? [];

        ['totalIeducar' => $totalIeducar] = ComponentBatchManagerService::sumPreviewCounts($previewCounts);
        ['totalIeducar' => $totalPostIeducar] = ComponentBatchManagerService::sumPreviewCounts($postCounts ?? []);

        $hasVerificationData = $postCounts || $postIdiario;
        $showVerification = in_array($status, [ComponentBatchStatus::COMPLETED, ComponentBatchStatus::RESTORED])
            || ($status === ComponentBatchStatus::FAILED && $hasVerificationData);

        return view('component-batch-manager.show', array_merge(
            $this->resolveNames($data),
            [
                'operation' => $componentBatchOperation,
                'status' => $status,
                'previewCounts' => $previewCounts,
                'totalIeducar' => $totalIeducar,
                'postCounts' => $postCounts,
                'postIdiario' => $postIdiario,
                'verificationWarnings' => $verificationWarnings,
                'showVerification' => $showVerification,
                'totalPostIeducar' => $totalPostIeducar,
                'isProcessing' => in_array($status, [ComponentBatchStatus::WAITING, ComponentBatchStatus::RUNNING]),
                'isFailed' => $status === ComponentBatchStatus::FAILED,
            ]
        ));
    }

    private function resolveNames(array $data): array
    {
        return [
            'schoolNames' => !empty($data['school_ids'])
                ? LegacySchool::whereIn('cod_escola', $data['school_ids'])->get()->map(fn ($s) => "{$s->name} ({$s->cod_escola})")->toArray()
                : [],
            'courseNames' => !empty($data['course_ids'])
                ? LegacyCourse::whereIn('cod_curso', $data['course_ids'])->get()->map(fn ($c) => "{$c->nm_curso} ({$c->cod_curso})")->toArray()
                : [],
            'gradeNames' => LegacyGrade::whereIn('cod_serie', $data['grade_ids'] ?? [])->get()->map(fn ($g) => "{$g->nm_serie} ({$g->cod_serie})")->toArray(),
            'disciplineNames' => LegacyDiscipline::query()
                ->whereIn('id', $data['discipline_ids'] ?? [])
                ->get()
                ->map(fn ($d) => "{$d->nome} ({$d->id})")
                ->toArray(),
        ];
    }
}
